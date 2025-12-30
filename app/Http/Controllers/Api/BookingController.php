<?php

namespace App\Http\Controllers\Api;

use App\DTOs\BookingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Services\BookingService;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;
use App\Exceptions\ValidationException as AppValidationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class BookingController extends Controller
{
    use ExceptionHandler, SuccessResponse, CanFilter;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * عرض قائمة حجوزات المستخدم الحالي مع فلاتر وترقيم
     */
    public function index(Request $request, BookingService $bookingService)
    {
        $perPage = (int) $request->get('per_page', 15);
        $user = $request->user();

        // Query لحجوزات هذا المستخدم فقط (كعميل أو طاهي)
        $query = $bookingService->getQueryForUser($user->id);

        // تطبيق الفلاتر العامة (بحث + مفاتيح خارجية)
        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $bookings = $query->latest()->paginate($perPage);

        // تحويل النتائج إلى DTO خفيفة للـ index
        $bookings->getCollection()->transform(function ($booking) {
            return BookingDTO::fromModel($booking)->toIndexArray();
        });

        return $this->collectionResponse($bookings, 'تم جلب قائمة الحجوزات بنجاح');
    }

    /**
     * إنشاء حجز جديد للمستخدم الحالي
     */
    public function store(StoreBookingRequest $request, BookingService $bookingService)
    {
        try {
            $validated = $request->validated();

            // إجبارياً نربط الحجز بالمستخدم الحالي كعميل
            $validated['customer_id'] = $request->user()->id;
            $validated['created_by'] = $request->user()->id;

            // Create BookingDTO
            $bookingDTO = new BookingDTO(
                null,
                $validated['customer_id'],
                $validated['chef_id'],
                $validated['chef_service_id'],
                $validated['address_id'] ?? null,
                $validated['date'],
                $validated['start_time'],
                $validated['hours_count'],
                $validated['number_of_guests'],
                $validated['service_type'],
                $validated['unit_price'],
                $validated['extra_guests_count'] ?? 0,
                $validated['extra_guests_amount'] ?? 0,
                $validated['total_amount'],
                $validated['commission_amount'] ?? 0,
                'pending',
                'pending',
                $validated['notes'] ?? null,
                true,
                $validated['created_by'],
                null
            );

            $result = $bookingService->createWithConflictCheck($bookingDTO);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'],
                    'conflicting_bookings' => $result['conflicting_bookings'] ?? []
                ], 409);
            }

            return $this->createdResponse(
                BookingDTO::fromModel($result['booking'])->toArray(),
                'تم إنشاء الحجز بنجاح'
            );
        } catch (AppValidationException $e) {
            return $e->render($request);
        }
    }

    /**
     * عرض حجز واحد للمستخدم الحالي
     */
    public function show(BookingService $bookingService, Request $request, $id)
    {
        try {
            $booking = $bookingService->findForUser(
                $id,
                $request->user()->id,
                ['customer', 'chef', 'service', 'address', 'transactions', 'rating']
            );

            $this->authorize('view', $booking);

            return $this->resourceResponse(
                BookingDTO::fromModel($booking)->toArray(),
                'تم جلب بيانات الحجز بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        }
    }

    /**
     * تحديث حجز يخص المستخدم الحالي
     */
    public function update(UpdateBookingRequest $request, BookingService $bookingService, $id)
    {
        try {
            $validated = $request->validated();

            // أولاً: نجلب الحجز المملوك للمستخدم الحالي
            $booking = $bookingService->findForUser(
                $id,
                $request->user()->id,
                ['customer', 'chef', 'service', 'address']
            );

            // ثانياً: نتحقق من الـ Policy
            $this->authorize('update', $booking);

            // نتأكد أن الحجز سيبقى منسوباً للمستخدم الحالي
            $validated['customer_id'] = $request->user()->id;
            $validated['updated_by'] = $request->user()->id;

            // ثالثاً: نحدّث نفس الـ Model (بدون إعادة استعلام جديد)
            $updated = $bookingService->updateModel($booking, $validated);

            return $this->updatedResponse(
                BookingDTO::fromModel($updated)->toArray(),
                'تم تحديث الحجز بنجاح'
            );
        } catch (AppValidationException $e) {
            return $e->render($request);
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        }
    }

    /**
     * إلغاء الحجز من العميل فقط
     */
    public function cancelByCustomer(BookingService $bookingService, Request $request, $id)
    {
        try {
            $booking = $bookingService->findForUser($id, $request->user()->id);

            // صلاحية خاصة بالعميل فقط
            $this->authorize('cancelByCustomer', $booking);

            // يسمح للعميل بالإلغاء فقط إذا كانت الحالة الحالية "pending" أو "accepted"
            if (!in_array($booking->booking_status, ['pending', 'accepted'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن للعميل إلغاء الحجز في حالته الحالية',
                    'errors' => [
                        'booking_status' => [
                            'الحالة الحالية لا تسمح بالإلغاء',
                            'current_status: ' . $booking->booking_status
                        ]
                    ]
                ], 422);
            }

            $cancelled = $bookingService->cancel($id, 'cancelled_by_customer');

            if (!$cancelled) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إلغاء الحجز'
                ], 500);
            }

            return $this->deletedResponse('تم إلغاء الحجز بنجاح');
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        }
    }

    /**
     * إلغاء حجز (نقطة عامة قد تُستخدم قديماً) — تُحدد الجهة تلقائياً
     */
    public function destroy(BookingService $bookingService, Request $request, $id)
    {
        try {
            $booking = $bookingService->findForUser($id, $request->user()->id);

            $this->authorize('delete', $booking);

            $user = $request->user();
            $reason = 'cancelled_by_customer';
            if ($user->chef && $booking->chef_id == $user->chef->id) {
                $reason = 'cancelled_by_chef';
            }

            // إذا كان الإلغاء من العميل تأكد من أن الحالة تسمح بذلك
            if ($reason === 'cancelled_by_customer' && !in_array($booking->booking_status, ['pending', 'accepted'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن للعميل إلغاء الحجز في حالته الحالية',
                    'errors' => [
                        'booking_status' => [
                            'الحالة الحالية لا تسمح بالإلغاء',
                            'current_status: ' . $booking->booking_status
                        ]
                    ]
                ], 422);
            }

            $cancelled = $bookingService->cancel($id, $reason);

            if (!$cancelled) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إلغاء الحجز'
                ], 500);
            }

            return $this->deletedResponse('تم إلغاء الحجز بنجاح');
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        }
    }

    /**
     * قبول الحجز (للطاهي)
     */
    public function accept(BookingService $bookingService, Request $request, $id)
    {
        try {
            $booking = $bookingService->findForUser($id, $request->user()->id);

            $this->authorize('accept', $booking);

            // الطاهي يستطيع قبول الحجز فقط عندما تكون الحالة الحالية "pending"
            if ($booking->booking_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن للطاهي قبول الحجز إلا إذا كانت حالته حالياً قيد الانتظار',
                    'errors' => [
                        'booking_status' => [
                            'الحالة الحالية لا تسمح بالقبول',
                            'current_status: ' . $booking->booking_status
                        ]
                    ]
                ], 422);
            }

            $accepted = $bookingService->accept($id);

            return $this->updatedResponse(
                BookingDTO::fromModel($accepted)->toArray(),
                'تم قبول الحجز بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        }
    }

    /**
     * رفض الحجز (للطاهي)
     */
    public function reject(BookingService $bookingService, Request $request, $id)
    {
        try {
            $booking = $bookingService->findForUser($id, $request->user()->id);

            $this->authorize('reject', $booking);

            // الطاهي يستطيع رفض الحجز فقط عندما تكون الحالة الحالية "pending"
            if ($booking->booking_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن للطاهي رفض الحجز إلا إذا كانت حالته حالياً قيد الانتظار',
                    'errors' => [
                        'booking_status' => [
                            'الحالة الحالية لا تسمح بالرفض',
                            'current_status: ' . $booking->booking_status
                        ]
                    ]
                ], 422);
            }

            $rejected = $bookingService->reject($id);

            return $this->updatedResponse(
                BookingDTO::fromModel($rejected)->toArray(),
                'تم رفض الحجز بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        }
    }

    /**
     * إلغاء الحجز من الطاهي (للطاهي)
     */
    public function cancelByChef(BookingService $bookingService, Request $request, $id)
    {
        try {
            $booking = $bookingService->findForUser($id, $request->user()->id);

            $this->authorize('cancelByChef', $booking);

            // الطاهي يستطيع إلغاء الحجز فقط عندما تكون الحالة الحالية "accepted"
            if ($booking->booking_status !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن للطاهي إلغاء الحجز إلا إذا كانت حالته مقبولة',
                    'errors' => [
                        'booking_status' => [
                            'الحالة الحالية لا تسمح بالإلغاء من الطاهي',
                            'current_status: ' . $booking->booking_status
                        ]
                    ]
                ], 422);
            }

            $cancelled = $bookingService->cancel($id, 'cancelled_by_chef');

            if (!$cancelled) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إلغاء الحجز'
                ], 500);
            }

            return $this->deletedResponse('تم إلغاء الحجز من الطاهي بنجاح');
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        }
    }

    /**
     * إكمال حجز يخص المستخدم الحالي (للطاهي)
     */
    public function complete(BookingService $bookingService, Request $request, $id)
    {
        try {
            $booking = $bookingService->findForUser($id, $request->user()->id);

            $this->authorize('complete', $booking);

            // الطاهي يستطيع إكمال الحجز فقط عندما تكون الحالة الحالية "accepted"
            if ($booking->booking_status !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن للطاهي إكمال الحجز إلا إذا كانت حالته مقبولة',
                    'errors' => [
                        'booking_status' => [
                            'الحالة الحالية لا تسمح بالإكمال',
                            'current_status: ' . $booking->booking_status
                        ]
                    ]
                ], 422);
            }

            $completed = $bookingService->complete($id);

            return $this->updatedResponse(
                BookingDTO::fromModel($completed)->toArray(),
                'تم إكمال الحجز بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        }
    }

    /**
     * فحص توفر الطاهي لفترة زمنية محددة
     */
    public function checkAvailability(Request $request, BookingService $bookingService, int $chefId)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'hours_count' => 'required|integer|min:1|max:12',
            'exclude_booking_id' => 'nullable|integer|exists:bookings,id'
        ]);

        try {
            $availability = $bookingService->checkAvailability(
                $chefId,
                $request->date,
                $request->start_time,
                $request->hours_count,
                $request->exclude_booking_id
            );

            return response()->json([
                'success' => true,
                'data' => $availability,
                'message' => $availability['available'] ? 'الوقت متاح' : 'الوقت غير متاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في فحص التوفر'
            ], 500);
        }
    }

    /**
     * جلب حجوزات الطاهي لتاريخ محدد
     */
    public function getChefBookings(Request $request, BookingService $bookingService, int $chefId)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            $bookings = $bookingService->getChefBookingsForDate($chefId, $request->date);

            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'تم جلب حجوزات الطاهي بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب حجوزات الطاهي'
            ], 500);
        }
    }

    /**
     * التحقق من صحة بيانات الحجز دون إنشاء
     */
    public function validateBooking(StoreBookingRequest $request, BookingService $bookingService)
    {
        try {
            $validated = $request->validated();

            $availability = $bookingService->checkAvailability(
                $validated['chef_id'],
                $validated['date'],
                $validated['start_time'],
                $validated['hours_count']
            );

            return response()->json([
                'success' => true,
                'data' => $availability,
                'message' => $availability['available'] ? 'بيانات الحجز صحيحة' : 'فشل التحقق من بيانات الحجز'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من بيانات الحجز'
            ], 500);
        }
    }

    /**
     * الحقول النصّية التي يمكن البحث فيها عبر CanFilter
     */
    protected function getSearchableFields(): array
    {
        return [
            'notes',
            'cancellation_reason',
        ];
    }

    /**
     * الفلاتر الخاصة بالمفاتيح الخارجية والقيم المنطقية
     */
    protected function getForeignKeyFilters(): array
    {
        return [
            'customer_id' => 'customer_id',
            'chef_id' => 'chef_id',
            'chef_service_id' => 'chef_service_id',
            'address_id' => 'address_id',
            'booking_status' => 'booking_status',
            'payment_status' => 'payment_status',
            'service_type' => 'service_type',
            'is_active' => 'is_active',
        ];
    }
}