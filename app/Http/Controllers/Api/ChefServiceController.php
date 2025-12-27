<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChefServiceService;
use App\Services\ChefServiceEquipmentService;
use App\DTOs\ChefServiceDTO;
use App\DTOs\ChefServiceEquipmentDTO;
use App\Http\Requests\StoreChefServiceRequest;
use App\Http\Requests\UpdateChefServiceRequest;
use App\Http\Requests\StoreChefServiceEquipmentRequest;
use App\Http\Requests\UpdateChefServiceEquipmentRequest;
use App\Http\Requests\BulkManageEquipmentRequest;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Http\Traits\CanFilter;
use App\Models\ChefService;
use App\Models\ChefServiceEquipment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
// Log statements removed
use App\Exceptions\ValidationException as AppValidationException;

class ChefServiceController extends Controller
{
    use ExceptionHandler, SuccessResponse, CanFilter;

    protected ChefServiceEquipmentService $equipmentService;

    public function __construct(ChefServiceEquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
        
        // Allow guests to view the services list and single service routes
        $this->middleware('auth:sanctum')->except(['index', 'show', 'getEquipment', 'showEquipment', 'showByChef']);
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

        // تحميل العلاقات مع التقييمات دائماً
        $relations = [
            'chef',
            'ratings' => function($query) {
                $query->with(['customer:id,first_name,last_name'])
                      ->where('chef_service_ratings.is_active', true)
                      ->orderBy('chef_service_ratings.created_at', 'desc')
                      ->limit(5); // أحدث 5 تقييمات فقط للقائمة
            }
        ];

        // include bookings and active ratings counts to avoid N+1 when transforming to DTO
        $services = $query->withCount([
            'bookings',
            // qualify the column name to avoid ambiguous `is_active` when joined in subquery
            'ratings' => function ($q) {
                $q->where('chef_service_ratings.is_active', true);
            }
        ])->with($relations)->latest()->paginate($perPage);

        // تحويل النتائج إلى DTO كاملة مع التقييمات
        $services->getCollection()->transform(function ($service) {
            return ChefServiceDTO::fromModel($service)->toArray();
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
            // Use general find مع تحميل الصور والتقييمات
            $service = $serviceService->find($id, [
                'chef',
                'images' => function($query) {
                    $query->where('is_active', true)->orderBy('created_at');
                },
                'ratings' => function($query) {
                    $query->with(['customer:id,first_name,last_name', 'booking:id,date'])
                          ->where('chef_service_ratings.is_active', true)
                          ->orderBy('chef_service_ratings.created_at', 'desc');
                }
            ]);

            return $this->resourceResponse(
                ChefServiceDTO::fromModel($service)->toArray(),
                'تم جلب بيانات الخدمة بنجاح'
            );
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException('الخدمة المطلوبة غير موجودة');
        }
    }

    /**
     * عرض خدمات شيف معين (قابل للفلاتر وترقيم)
     *
     * @param Request $request
     * @param ChefServiceService $serviceService
     * @param int $chefId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByChef(Request $request, ChefServiceService $serviceService, int $chefId)
    {
        $perPage = (int) $request->get('per_page', 10);

        // Query مخصص للشيف المحدد
        $query = $serviceService->getQueryForChef($chefId);

        // تطبيق الفلاتر المشتركة
        $query = $this->applyFilters(
            $query,
            $request,
            $this->getSearchableFields(),
            $this->getForeignKeyFilters()
        );

        $relations = [
            'chef',
            'ratings' => function($query) {
                $query->with(['customer:id,first_name,last_name'])
                      ->where('chef_service_ratings.is_active', true)
                      ->orderBy('chef_service_ratings.created_at', 'desc')
                      ->limit(5);
            }
        ];

        $services = $query->withCount([
            'bookings',
            'ratings' => function ($q) {
                $q->where('chef_service_ratings.is_active', true);
            }
        ])->with($relations)->latest()->paginate($perPage);

        $services->getCollection()->transform(function ($service) {
            return ChefServiceDTO::fromModel($service)->toArray();
        });

        return $this->collectionResponse($services, 'تم جلب خدمات الشيف بنجاح');
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

            $activated = $serviceService->activate($service->id);

                    // append bookings and ratings counts for this service to include in DTO
                    $service->bookings_count = $service->bookings()->count();
                    $service->ratings_count = $service->relationLoaded('ratings') ? $service->ratings->count() : $service->ratings()->where('is_active', true)->count();

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

    // ==================== Equipment Management Methods ====================

    /**
     * Display equipment for a specific service
     */
    public function getEquipment(Request $request, int $serviceId): JsonResponse
    {
        try {
            // Verify service exists
            $service = ChefService::findOrFail($serviceId);
            
            // Get equipment based on user authentication
            if (auth('sanctum')->check()) {
                // Authenticated user - show all equipment for service owner, active only for others
                try {
                    $this->authorize('update', $service);
                    // User can update service - show all equipment
                    $equipment = $this->equipmentService->getEquipmentForService($serviceId);
                    $data = ChefServiceEquipmentDTO::collectionForAdmin($equipment);
                } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                    // User cannot update service - show only active equipment
                    $equipment = $this->equipmentService->getEquipmentForService($serviceId);
                    $data = ChefServiceEquipmentDTO::collectionForClient($equipment);
                }
            } else {
                // Public access - show only active equipment
                $equipment = $this->equipmentService->getEquipmentForService($serviceId);
                $data = ChefServiceEquipmentDTO::collectionForClient($equipment);
            }

            return $this->successResponse($data, 'Equipment retrieved successfully.');
            
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Service not found.', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve equipment.', 500);
        }
    }

    /**
     * Store a newly created equipment
     */
    public function storeEquipment(StoreChefServiceEquipmentRequest $request): JsonResponse
    {
        try {
            $equipment = $this->equipmentService->create($request->validatedWithDefaults());
            $data = ChefServiceEquipmentDTO::fromModel($equipment)->toArray();
            
            return $this->successResponse($data, 'Equipment created successfully.', 201);
            
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create equipment.', 500);
        }
    }

    /**
     * Display the specified equipment
     */
    public function showEquipment(int $id): JsonResponse
    {
        try {
            $equipment = ChefServiceEquipment::with(['chefService'])
                                           ->findOrFail($id);
            
            // Check if user can view this equipment
            if (auth('sanctum')->check()) {
                try {
                    $this->authorize('update', $equipment->chefService);
                    // User can update service - show admin view
                    $data = ChefServiceEquipmentDTO::fromModel($equipment)->toAdminArray();
                } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                    // Non-owner can see all equipment
                    $data = ChefServiceEquipmentDTO::fromModel($equipment)->toClientArray();
                }
            } else {
                // Public access - show all equipment
                $data = ChefServiceEquipmentDTO::fromModel($equipment)->toClientArray();
            }

            return $this->successResponse($data, 'Equipment retrieved successfully.');
            
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Equipment not found.', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve equipment.', 500);
        }
    }

    /**
     * Update the specified equipment
     */
    public function updateEquipment(UpdateChefServiceEquipmentRequest $request, int $id): JsonResponse
    {
        try {
            $equipment = $this->equipmentService->update($id, $request->validatedWithDefaults());
            $data = ChefServiceEquipmentDTO::fromModel($equipment)->toArray();
            
            return $this->successResponse($data, 'Equipment updated successfully.');
            
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Equipment not found.', 404);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update equipment.', 500);
        }
    }

    /**
     * Remove the specified equipment
     */
    public function destroyEquipment(int $id): JsonResponse
    {
        try {
            $equipment = ChefServiceEquipment::findOrFail($id);
            
            // Check authorization
            $this->authorize('update', $equipment->chefService);
            
            $success = $this->equipmentService->delete($id);
            
            if ($success) {
                return $this->successResponse(null, 'Equipment deleted successfully.');
            } else {
                return $this->errorResponse('Failed to delete equipment.', 500);
            }
            
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Equipment not found.', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete equipment.', 500);
        }
    }

    /**
     * Bulk manage equipment for a service
     */
    public function bulkManageEquipment(BulkManageEquipmentRequest $request): JsonResponse
    {
        try {
            $validated = $request->validatedWithDefaults();
            $serviceId = $validated['chef_service_id'];
            
            // Delete equipment if specified
            if (!empty($validated['delete_ids'])) {
                $this->equipmentService->deleteEquipmentFromService($serviceId, $validated['delete_ids']);
            }
            
            // Update/create equipment
            $equipment = $this->equipmentService->updateServiceEquipment($serviceId, $validated['equipment']);
            $data = ChefServiceEquipmentDTO::collectionForAdmin($equipment);
            
            return $this->successResponse($data, 'Equipment updated successfully.');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update equipment.', 500);
        }
    }

    /**
     * Copy equipment from another service
     */
    public function copyEquipmentFromService(Request $request): JsonResponse
    {
        $request->validate([
            'to_service_id' => 'required|exists:chef_services,id',
            'from_service_id' => 'required|exists:chef_services,id',
            'equipment_ids' => 'sometimes|array',
            'equipment_ids.*' => 'exists:chef_service_equipment,id',
        ]);

        try {
            $toServiceId = $request->to_service_id;
            $fromServiceId = $request->from_service_id;
            
            // Verify user can update the target service
            $toService = ChefService::findOrFail($toServiceId);
            $this->authorize('update', $toService);
            
            // Verify user can view the source service
            $fromService = ChefService::findOrFail($fromServiceId);
            $this->authorize('view', $fromService);
            
            if ($request->has('equipment_ids')) {
                // Copy specific equipment
                $equipment = $this->equipmentService->addEquipmentFromService(
                    $toServiceId, 
                    $fromServiceId, 
                    $request->equipment_ids
                );
            } else {
                // Copy all equipment
                $equipment = $this->equipmentService->copyEquipmentBetweenServices($fromServiceId, $toServiceId);
            }
            
            $data = ChefServiceEquipmentDTO::collectionForAdmin($equipment);
            
            return $this->successResponse($data, 'Equipment copied successfully.');
            
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Service not found.', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to copy equipment.', 500);
        }
    }
}