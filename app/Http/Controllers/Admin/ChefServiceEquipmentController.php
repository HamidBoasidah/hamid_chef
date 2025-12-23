<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\ChefServiceEquipmentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkManageEquipmentRequest;
use App\Http\Requests\StoreChefServiceEquipmentRequest;
use App\Http\Requests\UpdateChefServiceEquipmentRequest;
use App\Models\ChefService;
use App\Models\ChefServiceEquipment;
use App\Services\ChefServiceEquipmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
 // Log statements removed
use Inertia\Inertia;
use Inertia\Response;

class ChefServiceEquipmentController extends Controller
{
    protected ChefServiceEquipmentService $equipmentService;

    public function __construct(ChefServiceEquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
        
        // Apply permissions middleware
        $this->middleware('permission:chef-services.view')->only(['index', 'show']);
        $this->middleware('permission:chef-services.create')->only(['create', 'store']);
        $this->middleware('permission:chef-services.edit')->only(['edit', 'update', 'bulkManage', 'processBulkManage']);
        $this->middleware('permission:chef-services.delete')->only(['destroy']);
    }

    /**
     * Display equipment for a specific service
     */
    public function index(Request $request, int $serviceId): Response|RedirectResponse
    {
        try {
            $service = ChefService::with(['chef:id,name,logo'])->findOrFail($serviceId);
            $equipment = $this->equipmentService->getEquipmentForService($serviceId);
            $equipmentData = ChefServiceEquipmentDTO::collectionForAdmin($equipment);
            $summary = $this->equipmentService->getEquipmentSummary($serviceId);

            return Inertia::render('Admin/ChefServices/Equipment/Index', [
                'service' => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'chef' => [
                        'id' => $service->chef->id,
                        'name' => $service->chef->name,
                        'logo' => $service->chef->logo,
                    ],
                ],
                'equipment' => $equipmentData,
                'summary' => ChefServiceEquipmentDTO::equipmentSummary($summary),
                'can' => [
                    'create' => request()->user()->can('chef-services.create'),
                    'edit' => request()->user()->can('chef-services.edit'),
                    'delete' => request()->user()->can('chef-services.delete'),
                ],
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.chef-services.index')
                           ->with('error', 'Service not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.chef-services.index')
                           ->with('error', 'Failed to load equipment.');
        }
    }

    /**
     * Show the form for creating new equipment
     */
    public function create(int $serviceId): Response|RedirectResponse
    {
        try {
            $service = ChefService::with(['chef:id,name'])->findOrFail($serviceId);

            return Inertia::render('Admin/ChefServices/Equipment/Create', [
                'service' => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'chef' => [
                        'id' => $service->chef->id,
                        'name' => $service->chef->name,
                    ],
                ],
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.chef-services.index')
                           ->with('error', 'Service not found.');
        }
    }

    /**
     * Store a newly created equipment
     */
    public function store(StoreChefServiceEquipmentRequest $request): RedirectResponse
    {
        try {
            $equipment = $this->equipmentService->create($request->validatedWithDefaults());
            
            return redirect()->route('admin.chef-service-equipment.index', $equipment->chef_service_id)
                           ->with('success', 'Equipment created successfully.');
            
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create equipment.');
        }
    }

    /**
     * Display the specified equipment
     */
    public function show(int $id): Response|RedirectResponse
    {
        try {
            $equipment = ChefServiceEquipment::with(['chefService.chef'])
                                           ->findOrFail($id);
            
            $data = ChefServiceEquipmentDTO::fromModel($equipment)->toAdminArray();

            return Inertia::render('Admin/ChefServices/Equipment/Show', [
                'equipment' => $data,
                'service' => [
                    'id' => $equipment->chefService->id,
                    'name' => $equipment->chefService->name,
                    'chef' => [
                        'id' => $equipment->chefService->chef->id,
                        'name' => $equipment->chefService->chef->name,
                    ],
                ],
                'can' => [
                    'edit' => request()->user()->can('chef-services.edit'),
                    'delete' => request()->user()->can('chef-services.delete'),
                ],
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.chef-services.index')
                           ->with('error', 'Equipment not found.');
        }
    }

    /**
     * Show the form for editing equipment
     */
    public function edit(int $id): Response|RedirectResponse
    {
        try {
            $equipment = ChefServiceEquipment::with(['chefService.chef'])->findOrFail($id);
            $data = ChefServiceEquipmentDTO::fromModel($equipment)->toAdminArray();

            return Inertia::render('Admin/ChefServices/Equipment/Edit', [
                'equipment' => $data,
                'service' => [
                    'id' => $equipment->chefService->id,
                    'name' => $equipment->chefService->name,
                    'chef' => [
                        'id' => $equipment->chefService->chef->id,
                        'name' => $equipment->chefService->chef->name,
                    ],
                ],
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.chef-services.index')
                           ->with('error', 'Equipment not found.');
        }
    }

    /**
     * Update the specified equipment
     */
    public function update(UpdateChefServiceEquipmentRequest $request, int $id): RedirectResponse
    {
        try {
            $equipment = $this->equipmentService->update($id, $request->validatedWithDefaults());
            
            return redirect()->route('admin.chef-service-equipment.index', $equipment->chef_service_id)
                           ->with('success', 'Equipment updated successfully.');
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.chef-services.index')
                           ->with('error', 'Equipment not found.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update equipment.');
        }
    }

    /**
     * Remove the specified equipment
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $equipment = ChefServiceEquipment::findOrFail($id);
            $serviceId = $equipment->chef_service_id;
            
            $success = $this->equipmentService->delete($id);
            
            if ($success) {
                return redirect()->route('admin.chef-service-equipment.index', $serviceId)
                               ->with('success', 'Equipment deleted successfully.');
            } else {
                return redirect()->back()
                               ->with('error', 'Failed to delete equipment.');
            }
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.chef-services.index')
                           ->with('error', 'Equipment not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to delete equipment.');
        }
    }

    /**
     * Show bulk management page
     */
    public function bulkManage(int $serviceId): Response|RedirectResponse
    {
        try {
            $service = ChefService::with(['chef:id,name'])->findOrFail($serviceId);
            $equipment = $this->equipmentService->getEquipmentForService($serviceId);
            $equipmentData = ChefServiceEquipmentDTO::collectionForAdmin($equipment);
            
            // Get other services from the same chef for copying
            $otherServices = ChefService::where('chef_id', $service->chef_id)
                                      ->where('id', '!=', $serviceId)
                                      ->where('is_active', true)
                                      ->select('id', 'name')
                                      ->get();

            return Inertia::render('Admin/ChefServices/Equipment/BulkManage', [
                'service' => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'chef' => [
                        'id' => $service->chef->id,
                        'name' => $service->chef->name,
                    ],
                ],
                'equipment' => $equipmentData,
                'other_services' => $otherServices,
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.chef-services.index')
                           ->with('error', 'Service not found.');
        }
    }

    /**
     * Process bulk management
     */
    public function processBulkManage(BulkManageEquipmentRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validatedWithDefaults();
            $serviceId = $validated['chef_service_id'];
            
            // Delete equipment if specified
            if (!empty($validated['delete_ids'])) {
                $this->equipmentService->deleteEquipmentFromService($serviceId, $validated['delete_ids']);
            }
            
            // Update/create equipment
            $this->equipmentService->updateServiceEquipment($serviceId, $validated['equipment']);
            
            return redirect()->route('admin.chef-service-equipment.index', $serviceId)
                           ->with('success', 'Equipment updated successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update equipment.');
        }
    }


}