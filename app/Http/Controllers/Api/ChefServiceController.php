<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChefServiceService;
use App\DTOs\ChefServiceDTO;
use App\Http\Requests\StoreChefServiceRequest;
use App\Http\Requests\UpdateChefServiceRequest;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use App\Exceptions\ValidationException as AppValidationException;

class ChefServiceController extends Controller
{
    use ExceptionHandler, SuccessResponse, CanFilter;

    public function __construct()
    {
        // Allow guests to view the services list and single service routes
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * عرض قائمة الخدمات العامة (متاحة لأي مستخدم) مع فلاتر وترقيم
     */
    public function index(Request $request, ChefServiceService $serviceService)
    {
        $perPage = (int) $request->get('per_page', 10);

        // Query للخدمات النشطة العامة (العلاقات محملة تلقائياً من Repository)
        $query = $serviceService->getActiveServices();

        // تطبيق الفلاتر العامة (بحث + مفاتيح خارجية)
        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $services = $query->latest()->paginate($perPage);

        // تحويل النتائج إلى DTO خفيفة للـ index
        $services->getCollection()->transform(function ($service) {
            return ChefServiceDTO::fromModel($service)->toIndexArray();
        });

        return $this->collectionResponse($services, 'تم جلب قائمة الخدمات بنجاح');
    }

    /**
     * إنشاء خدمة جديدة
     */
    public function store(StoreChefServiceRequest $request, ChefServiceService $serviceService)
    {
        try {
            $data = $request->validated();

            $service = $serviceService->create($data);

            return $this->createdResponse(
                ChefServiceDTO::fromModel($service)->toArray(),
                'تم إنشاء الخدمة بنجاح'
            );
        } catch (AppValidationException $e) {
            return $e->render($request);
        } catch (QueryException $e) {
            throw $e;
        }
    }

    /**
     * عرض خدمة واحدة
     */
    public function show(ChefServiceService $serviceService, Request $request, $id)
    {
        try {
            // Use general find مع تحميل الصور للصور النشطة فقط
            $service = $serviceService->find($id, [
                'images' => function($query) {
                    $query->where('is_active', true)->orderBy('created_at');
                }
            ]);

            $this->authorize('view', $service);

            return $this->resourceResponse(
                ChefServiceDTO::fromModel($service)->toArray(),
                'تم جلب بيانات الخدمة بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الخدمة المطلوبة غير موجودة');
        }
    }

    /**
     * تحديث خدمة
     */
    public function update(UpdateChefServiceRequest $request, ChefServiceService $serviceService, $id)
    {
        try {
            $data = $request->validated();

            // أولاً: نجلب الخدمة مع الصور للصور النشطة فقط
            $service = $serviceService->find($id, [
                'images' => function($query) {
                    $query->where('is_active', true)->orderBy('created_at');
                }
            ]);

            // ثانياً: نتحقق من الـ Policy
            $this->authorize('update', $service);

            // ثالثاً: نحدّث نفس الـ Model (بدون إعادة استعلام جديد)
            $updated = $serviceService->updateModel($service, $data);

            // إعادة تحميل الصور بعد التحديث للحصول على البيانات المحدثة
            $updated->load([
                'images' => function($query) {
                    $query->where('is_active', true)->orderBy('created_at');
                }
            ]);

            return $this->updatedResponse(
                ChefServiceDTO::fromModel($updated)->toArray(),
                'تم تحديث الخدمة بنجاح'
            );
        } catch (AppValidationException $e) {
            return $e->render($request);
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الخدمة المطلوبة غير موجودة');
        }
    }

    /**
     * حذف خدمة (مع التحقق من الحجوزات)
     */
    public function destroy(ChefServiceService $serviceService, Request $request, $id)
    {
        $service = null;

        try {
            $service = $serviceService->find($id);

            $this->authorize('delete', $service);

            // منع حذف خدمة مرتبطة بحجوزات
            if (method_exists($service, 'bookings') && $service->bookings()->exists()) {
                $this->throwResourceInUseException('لا يمكن حذف خدمة مرتبطة بحجوزات');
            }

            $serviceService->delete($service->id);

            return $this->deletedResponse('تم حذف الخدمة بنجاح');
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الخدمة المطلوبة غير موجودة');
        } catch (QueryException $e) {
            if ($service) {
                $this->handleDatabaseException($e, $service, [
                    'bookings' => 'حجوزات',
                ]);
            }

            throw $e;
        }
    }

    /**
     * تفعيل خدمة
     */
    public function activate(ChefServiceService $serviceService, Request $request, $id)
    {
        try {
            $service = $serviceService->find($id);

            $this->authorize('activate', $service);

            $activated = $serviceService->activate($service->id);

            return $this->activatedResponse(
                ChefServiceDTO::fromModel($activated)->toArray(),
                'تم تفعيل الخدمة بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الخدمة المطلوبة غير موجودة');
        }
    }

    /**
     * تعطيل خدمة
     */
    public function deactivate(ChefServiceService $serviceService, Request $request, $id)
    {
        try {
            $service = $serviceService->find($id);

            $this->authorize('deactivate', $service);

            $deactivated = $serviceService->deactivate($service->id);

            return $this->deactivatedResponse(
                ChefServiceDTO::fromModel($deactivated)->toArray(),
                'تم تعطيل الخدمة بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الخدمة المطلوبة غير موجودة');
        }
    }

    /**
     * الحقول النصّية التي يمكن البحث فيها عبر CanFilter
     */
    protected function getSearchableFields(): array
    {
        return [
            'name',
            'description',
        ];
    }

    /**
     * الفلاتر الخاصة بالمفاتيح الخارجية والقيم المنطقية
     */
    protected function getForeignKeyFilters(): array
    {
        return [
            'chef_id'      => 'chef_id',
            'service_type' => 'service_type',
            'is_active'    => 'is_active',
        ];
    }
}