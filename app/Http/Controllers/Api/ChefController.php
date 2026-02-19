<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChefService;
use App\Services\ChefAvailabilityService;
use App\DTOs\ChefDTO;
use App\Http\Requests\StoreChefRequest;
use App\Http\Requests\UpdateChefRequest;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use App\Exceptions\ValidationException as AppValidationException;

class ChefController extends Controller
{
    use ExceptionHandler, SuccessResponse, CanFilter;

    public function __construct()
    {
        // Allow guests to view the chefs list, single chef, by-category routes, availability, and nearest
        $this->middleware('auth:sanctum')->except(['index', 'show', 'byCategory', 'availability', 'nearest']);
    }

    /**
     * عرض قائمة الطهاة العامة (متاحة لأي مستخدم) مع فلاتر وترقيم
     */
    public function index(Request $request, ChefService $chefService)
    {
        $perPage = (int) $request->get('per_page', 10);

        // Query للطهاة النشطين العامين (العلاقات محملة تلقائياً من Repository)
        $query = $chefService->getActiveChefs();

        // تطبيق الفلاتر العامة (بحث + مفاتيح خارجية)
        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $chefs = $query->latest()->paginate($perPage);

        // تحويل النتائج إلى DTO خفيفة للـ index
        $chefs->getCollection()->transform(function ($chef) {
            return ChefDTO::fromModel($chef)->toIndexArray();
        });

        return $this->collectionResponse($chefs, 'تم جلب قائمة الطهاة بنجاح');
    }

    /**
     * جلب الطهاة بحسب القسم (عام - متاحة للزوار)
     */
    public function byCategory(Request $request, ChefService $chefService, $categoryId)
    {
        $perPage = (int) $request->get('per_page', 10);

        // Query for chefs in the given category (only active chefs)
        $query = $chefService->getChefsByCategory($categoryId);

        // Apply generic filters (search + foreign key filters)
        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $chefs = $query->latest()->paginate($perPage);

        // Transform results to DTO for index
        $chefs->getCollection()->transform(function ($chef) {
            return ChefDTO::fromModel($chef)->toIndexArray();
        });

        return $this->collectionResponse($chefs, 'تم جلب قائمة الطهاة بالقسم بنجاح');
    }

    /**
     * إنشاء طاهي جديد للمستخدم الحالي
     */
    public function store(StoreChefRequest $request, ChefService $chefService)
    {
        try {
            $data = $request->validated();

            // Allow the service to attach the current user_id (same pattern as AddressService::create)

            $chef = $chefService->create($data);

            return $this->createdResponse(
                ChefDTO::fromModel($chef)->toArray(),
                'تم إنشاء الطاهي بنجاح'
            );
        } catch (AppValidationException $e) {
            return $e->render($request);
        } catch (QueryException $e) {
            // Handle database constraint violations using ChefService
            return $chefService->handleChefCreationDatabaseException($e, $request);
        }
    }



    /**
     * عرض طاهي واحد للمستخدم الحالي
     */
    public function show(ChefService $chefService, Request $request, $id)
    {
        try {
            // Use general find مع تحميل المعرض للصور النشطة فقط
            $chef = $chefService->find($id, [
                // keep categories loaded (defaultWith is overridden when passing $with)
                'categories:id,name,slug',
                'gallery' => function($query) {
                        $query->where('is_active', true)->orderBy('created_at');
                    },
                    // include active services of the chef
                'services' => function($query) {
                        $query->where('is_active', true)->orderBy('created_at');
                    }
            ]);

            return $this->resourceResponse(
                ChefDTO::fromModel($chef)->toArray(),
                'تم جلب بيانات الطاهي بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الطاهي المطلوب غير موجود');
        }
    }

    /**
     * جلب طاهي بواسطة user_id
     */
    public function showByUserId(ChefService $chefService, Request $request, $userId)
    {
        try {
            $chef = $chefService->findForCurrentUser($userId, [
                'categories:id,name,slug',
                'gallery' => function($query) {
                    $query->where('is_active', true)->orderBy('created_at');
                },
                'services' => function($query) {
                    $query->where('is_active', true)->orderBy('created_at');
                }
            ]);

            return $this->resourceResponse(
                ChefDTO::fromModel($chef)->toArray(),
                'تم جلب بيانات الطاهي بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الطاهي المطلوب غير موجود');
        }
    }

    /**
     * تحديث طاهي يخص المستخدم الحالي
     */
    public function update(UpdateChefRequest $request, ChefService $chefService, $id)
    {
        try {
            $data = $request->validated();

            // أولاً: نجلب الطاهي المملوك للمستخدم الحالي مع المعرض للصور النشطة فقط
            $chef = $chefService->findForUser($id, $request->user()->id, [
                'gallery' => function($query) {
                    $query->where('is_active', true)->orderBy('created_at');
                }
            ]);

            // ثانياً: نتحقق من الـ Policy
            $this->authorize('update', $chef);

            // نتأكد أن الطاهي سيبقى منسوباً للمستخدم الحالي
            $data['user_id'] = $request->user()->id;

            // ثالثاً: نحدّث نفس الـ Model (بدون إعادة استعلام جديد)
            $updated = $chefService->updateModel($chef, $data);

            // إعادة تحميل المعرض بعد التحديث للحصول على البيانات المحدثة
            $updated->load([
                'gallery' => function($query) {
                    $query->where('is_active', true)->orderBy('created_at');
                }
            ]);

            return $this->updatedResponse(
                ChefDTO::fromModel($updated)->toArray(),
                'تم تحديث الطاهي بنجاح'
            );
        } catch (AppValidationException $e) {
            return $e->render($request);
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الطاهي المطلوب غير موجود');
        }
    }

    /**
     * حذف طاهي يخص المستخدم الحالي (مع التحقق من الحجوزات والخدمات)
     */
    public function destroy(ChefService $chefService, Request $request, $id)
    {
        $chef = null;

        try {
            $chef = $chefService->findForUser($id, $request->user()->id);

            $this->authorize('delete', $chef);

            // منع حذف طاهي مرتبط بحجوزات أو خدمات
            if (method_exists($chef, 'bookings') && $chef->bookings()->exists()) {
                $this->throwResourceInUseException('لا يمكن حذف طاهي مرتبط بحجوزات');
            }

            if (method_exists($chef, 'services') && $chef->services()->exists()) {
                $this->throwResourceInUseException('لا يمكن حذف طاهي مرتبط بخدمات');
            }

            $chefService->delete($chef->id);

            return $this->deletedResponse('تم حذف الطاهي بنجاح');
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الطاهي المطلوب غير موجود');
        } catch (QueryException $e) {
            if ($chef) {
                $this->handleDatabaseException($e, $chef, [
                    'bookings' => 'حجوزات',
                    'services' => 'خدمات',
                ]);
            }

            throw $e;
        }
    }

    /**
     * تفعيل طاهي يخص المستخدم الحالي
     */
    public function activate(ChefService $chefService, Request $request, $id)
    {
        try {
            $chef = $chefService->findForUser($id, $request->user()->id);

            $this->authorize('activate', $chef);

            $activated = $chefService->activate($chef->id);

            return $this->activatedResponse(
                ChefDTO::fromModel($activated)->toArray(),
                'تم تفعيل الطاهي بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الطاهي المطلوب غير موجود');
        }
    }

    /**
     * تعطيل طاهي يخص المستخدم الحالي
     */
    public function deactivate(ChefService $chefService, Request $request, $id)
    {
        try {
            $chef = $chefService->findForUser($id, $request->user()->id);

            $this->authorize('deactivate', $chef);

            $deactivated = $chefService->deactivate($chef->id);

            return $this->deactivatedResponse(
                ChefDTO::fromModel($deactivated)->toArray(),
                'تم تعطيل الطاهي بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الطاهي المطلوب غير موجود');
        }
    }

    /**
     * البحث عن أقرب الشيفات بناءً على موقع المستخدم
     *
     * GET /api/chefs/nearest
     *
     * Query Parameters:
     * - lat: (required) خط العرض
     * - lng: (required) خط الطول
     * - radius: (optional) نصف القطر بالكيلومتر (افتراضي 50)
     * - per_page: (optional) عدد النتائج في الصفحة (افتراضي 10)
     * - category_id: (optional) فلتر حسب التصنيف
     * - tag_id: (optional) فلتر حسب الوسم
     * - min_rating: (optional) الحد الأدنى للتقييم
     * - max_price: (optional) الحد الأقصى للسعر بالساعة
     * - governorate_id: (optional) فلتر حسب المحافظة
     * - search: (optional) بحث نصي شامل (اسم الشيف، الخدمات، الوسوم، التصنيفات)
     *
     * Returns:
     * - قائمة الشيفات مرتبة من الأقرب للأبعد
     * - كل شيف يحتوي على: البيانات الأساسية، التقييم، المسافة التقريبية
     */
    public function nearest(Request $request, ChefService $chefService)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1|max:500',
            'per_page' => 'nullable|integer|min:1|max:100',
            'category_id' => 'nullable|integer|exists:categories,id',
            'tag_id' => 'nullable|integer|exists:tags,id',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'max_price' => 'nullable|numeric|min:0',
            'governorate_id' => 'nullable|integer|exists:governorates,id',
            'search' => 'nullable|string|max:100',
        ]);

        try {
            $latitude = (float) $request->input('lat');
            $longitude = (float) $request->input('lng');
            $radius = (int) $request->input('radius', 50);
            $perPage = (int) $request->input('per_page', 10);

            $filters = [
                'category_id' => $request->input('category_id'),
                'tag_id' => $request->input('tag_id'),
                'min_rating' => $request->input('min_rating'),
                'max_price' => $request->input('max_price'),
                'governorate_id' => $request->input('governorate_id'),
                'search' => $request->input('search'),
            ];

            $chefs = $chefService->searchNearestChefs(
                $latitude,
                $longitude,
                $radius,
                $perPage,
                $filters
            );

            // تحويل النتائج إلى DTO مع إضافة المسافة
            $chefs->getCollection()->transform(function ($chef) {
                $data = ChefDTO::fromModel($chef)->toIndexArray();
                $data['distance_km'] = round($chef->distance, 2);
                $data['distance_text'] = $this->formatDistance($chef->distance);
                return $data;
            });

            return $this->collectionResponse($chefs, 'تم جلب قائمة الشيفات القريبين بنجاح');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البحث عن الشيفات القريبين',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تنسيق المسافة بشكل مقروء
     */
    protected function formatDistance(float $distanceKm): string
    {
        if ($distanceKm < 1) {
            $meters = round($distanceKm * 1000);
            return "{$meters} متر";
        }

        return round($distanceKm, 1) . " كم";
    }

    /**
     * Get chef availability calendar and day details
     *
     * POST /api/chefs/{chefId}/availability-calendar
     *
     * Body:
     * - date: Target date (optional, defaults to today)
     * - chef_service_id: Get service details only (optional)
     *
     * Important:
     * - Bookings are fetched based on chef_id (ALL chef bookings)
     * - chef_service_id is used ONLY for service details (name, min_hours, rest_hours)
     * - Availability calculation considers ALL chef bookings regardless of service
     *
     * Returns:
     * - Service details (if chef_service_id provided) - name, min_hours, rest_hours
     * - Available days (working days with no bookings)
     * - Off days (days the chef doesn't work)
     * - Vacation days
     * - Partially booked days
     * - Fully booked days
     * - Day details for a specific date (working hours, ALL bookings, available slots)
     */
    public function availability(Request $request, ChefAvailabilityService $availabilityService, $chefId)
    {
        $request->validate([
            'date' => 'nullable|date',
            'chef_service_id' => 'nullable|integer|exists:chef_services,id',
        ]);

        try {
            $date = $request->input('date');
            $chefServiceId = $request->input('chef_service_id');

            $availability = $availabilityService->getChefAvailability(
                (int) $chefId,
                $date,
                $chefServiceId
            );

            return $this->successResponse(
                $availability,
                'تم جلب بيانات التوفر بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الطاهي المطلوب غير موجود');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات التوفر',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحقول النصّية التي يمكن البحث فيها عبر CanFilter
     */
    protected function getSearchableFields(): array
    {
        return [
            'name',
            'email',
            'phone',
            'address',
            'short_description',
            'long_description',
        ];
    }

    /**
     * الفلاتر الخاصة بالمفاتيح الخارجية والقيم المنطقية
     */
    protected function getForeignKeyFilters(): array
    {
        return [
            'user_id'        => 'user_id',
            'governorate_id' => 'governorate_id',
            'district_id'    => 'district_id',
            'area_id'        => 'area_id',
            'is_active'      => 'is_active',
            'status'         => 'status',
        ];
    }
}
