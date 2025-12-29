<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ChefServiceRatingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChefServiceRatingRequest;
use App\Http\Requests\UpdateChefServiceRatingRequest;
use App\Services\ChefServiceRatingService;
use App\Services\BookingService;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use App\Exceptions\ValidationException as AppValidationException;

class ChefServiceRatingController extends Controller
{
    use ExceptionHandler, SuccessResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * عرض تقييم يخص مستخدماً محدداً
     * - يتطلب أن يطابق userId المستخدم المصادَق عليه
     * - خيارياً: تمرير booking_id كـ query لاستخراج تقييم حجز محدد
     */
    public function showByUser(Request $request, ChefServiceRatingService $ratingService, int $userId)
    {
        try {
            $authUser = $request->user();

            // السماح للمستخدم برؤية تقييماته فقط
            if ((int) $authUser->id !== (int) $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح بالوصول إلى هذه البيانات'
                ], 403);
            }

            // booking relation must reference the correct foreign key column name: chef_service_id
            $with = ['booking:id,date,chef_id,chef_service_id', 'chef:id,name'];
            $bookingId = $request->query('booking_id');
            $rating = null;

            if ($bookingId) {
                // احضر تقييم هذا الحجز (إن وُجد) وتأكد أنه يخص المستخدم
                $found = $ratingService->getForBooking((int) $bookingId, $with);
                if ($found && (int) $found->customer_id === (int) $authUser->id) {
                    $rating = $found;
                }
            } else {
                // احضر أحدث تقييم للمستخدم (إن وُجد)
                $ratings = $ratingService->getForCustomer($authUser->id, $with);
                $rating = $ratings->first();
            }

            return response()->json([
                'success' => true,
                'rating' => $rating ? ChefServiceRatingDTO::fromModel($rating)->toArray() : null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * إنشاء تقييم جديد للمستخدم الحالي
     */
    public function store(StoreChefServiceRatingRequest $request, ChefServiceRatingService $ratingService, BookingService $bookingService)
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            // التحقق من أن الحجز يخص المستخدم الحالي
            $booking = $bookingService->findForUser(
                $data['booking_id'],
                $user->id,
                ['chef', 'service']
            );

            // التحقق من أن الحجز مكتمل فقط
            if ($booking->booking_status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'يمكن تقييم الحجوزات المكتملة فقط'
                ], 400);
            }

            // التحقق من عدم وجود تقييم سابق لنفس الحجز
            if ($booking->rating()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'تم تقييم هذا الحجز مسبقاً'
                ], 400);
            }

            // إجبارياً نربط التقييم بالمستخدم الحالي والطاهي من الحجز
            $data['customer_id'] = $user->id;
            $data['chef_id'] = $booking->chef_id;
            $data['created_by'] = $user->id;

            $rating = $ratingService->create($data);

            return $this->createdResponse(
                ChefServiceRatingDTO::fromModel($rating)->toArray(),
                'تم إنشاء التقييم بنجاح'
            );
        } catch (AppValidationException $e) {
            return $e->render($request);
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الحجز المطلوب غير موجود');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * تحديث تقييم يخص المستخدم الحالي
     */
    public function update(UpdateChefServiceRatingRequest $request, ChefServiceRatingService $ratingService, $id)
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            // البحث عن التقييم الذي يخص المستخدم الحالي
            $rating = $ratingService->findForCustomer($id, $user->id, ['booking', 'chef']);

            // إضافة معلومات التحديث
            $data['updated_by'] = $user->id;

            // تحديث التقييم
            $updated = $ratingService->updateModel($rating, $data);

            return $this->updatedResponse(
                ChefServiceRatingDTO::fromModel($updated)->toArray(),
                'تم تحديث التقييم بنجاح'
            );
        } catch (AppValidationException $e) {
            return $e->render($request);
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('التقييم المطلوب غير موجود');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * حذف تقييم يخص المستخدم الحالي
     */
    public function destroy(ChefServiceRatingService $ratingService, Request $request, $id)
    {
        try {
            $user = $request->user();

            // البحث عن التقييم الذي يخص المستخدم الحالي
            $rating = $ratingService->findForCustomer($id, $user->id);

            // حذف التقييم
            $ratingService->delete($rating->id);

            return $this->deletedResponse('تم حذف التقييم بنجاح');
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('التقييم المطلوب غير موجود');
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل حذف التقييم'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}