<?php

namespace App\Services;

use App\Models\ChefServiceEquipment;
use App\Repositories\ChefServiceEquipmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
// Log statements removed

class ChefServiceEquipmentService
{
    protected ChefServiceEquipmentRepository $repository;

    public function __construct(ChefServiceEquipmentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new equipment
     */
    public function create(array $attributes): ChefServiceEquipment
    {
        // Validate equipment name uniqueness for the service
        if ($this->repository->equipmentNameExists($attributes['chef_service_id'], $attributes['name'])) {
            throw new \InvalidArgumentException('Equipment with this name already exists for this service.');
        }

        return $this->repository->create($attributes);
    }

    /**
     * Update equipment by ID
     */
    public function update(int $id, array $attributes): ChefServiceEquipment
    {
        $equipment = $this->repository->findOrFail($id);
        return $this->updateModel($equipment, $attributes);
    }

    /**
     * Update equipment model
     */
    public function updateModel(ChefServiceEquipment $equipment, array $attributes): ChefServiceEquipment
    {
        // Validate equipment name uniqueness if name is being changed
        if (isset($attributes['name']) && $attributes['name'] !== $equipment->name) {
            if ($this->repository->equipmentNameExists($equipment->chef_service_id, $attributes['name'], $equipment->id)) {
                throw new \InvalidArgumentException('Equipment with this name already exists for this service.');
            }
        }

        return $this->repository->update($equipment->id, $attributes);
    }

    /**
     * Delete equipment
     */
    public function delete(int $id): bool
    {
        $equipment = $this->repository->findOrFail($id);
        return $this->repository->delete($equipment->id);
    }

    /**
     * Get equipment for a specific service
     */
    public function getEquipmentForService(int $serviceId): Collection
    {
        return $this->repository->getEquipmentForService($serviceId);
    }

    /**
     * Get active equipment for a specific service
     */
    public function getActiveEquipmentForService(int $serviceId): Collection
    {
        return $this->repository->getActiveEquipmentForService($serviceId);
    }

    /**
     * Create multiple equipment for a service
     */
    public function createEquipmentForService(int $serviceId, array $equipmentData): Collection
    {
        DB::beginTransaction();
        try {
            $createdEquipment = collect();

            foreach ($equipmentData as $data) {
                $data['chef_service_id'] = $serviceId;
                $equipment = $this->create($data);
                $createdEquipment->push($equipment);
            }

            DB::commit();
            return $createdEquipment;
        } catch (\Exception $e) {
            DB::rollBack();
            // logging removed
            throw $e;
        }
    }

    /**
     * Update service equipment (bulk operation)
     */
    public function updateServiceEquipment(int $serviceId, array $equipmentData): Collection
    {
        DB::beginTransaction();
        try {
            // Add service ID to all equipment data
            foreach ($equipmentData as &$data) {
                $data['chef_service_id'] = $serviceId;
            }

            $this->repository->updateMultipleEquipment($equipmentData);

            DB::commit();
            return $this->getEquipmentForService($serviceId);
        } catch (\Exception $e) {
            DB::rollBack();
            // logging removed
            throw $e;
        }
    }

    /**
     * Delete equipment from service
     */
    public function deleteEquipmentFromService(int $serviceId, array $equipmentIds): bool
    {
        try {
            $deletedCount = $this->repository->deleteEquipmentForService($serviceId, $equipmentIds);
            return $deletedCount > 0;
        } catch (\Exception $e) {
            // logging removed
            return false;
        }
    }

    /**
     * Copy equipment between services
     */
    public function copyEquipmentBetweenServices(int $fromServiceId, int $toServiceId): Collection
    {
        DB::beginTransaction();
        try {
            $success = $this->repository->duplicateEquipmentToService($fromServiceId, $toServiceId);
            
            if (!$success) {
                throw new \Exception('No equipment found to copy or copy operation failed.');
            }

            DB::commit();
            return $this->getEquipmentForService($toServiceId);
        } catch (\Exception $e) {
            DB::rollBack();
            // logging removed
            throw $e;
        }
    }

    /**
     * Add equipment from another service
     */
    public function addEquipmentFromService(int $toServiceId, int $fromServiceId, array $equipmentIds): Collection
    {
        DB::beginTransaction();
        try {
            $sourceEquipment = $this->repository->query()
                                               ->whereIn('id', $equipmentIds)
                                               ->where('chef_service_id', $fromServiceId)
                                               ->get();

            if ($sourceEquipment->isEmpty()) {
                throw new \Exception('No equipment found to copy.');
            }

            foreach ($sourceEquipment as $equipment) {
                // Check if equipment with same name already exists
                if (!$this->repository->equipmentNameExists($toServiceId, $equipment->name)) {
                    $this->repository->create([
                        'chef_service_id' => $toServiceId,
                        'name' => $equipment->name,
                        'is_included' => $equipment->is_included,
                    ]);
                }
            }

            DB::commit();
            return $this->getEquipmentForService($toServiceId);
        } catch (\Exception $e) {
            DB::rollBack();
            // logging removed
            throw $e;
        }
    }

    /**
     * Validate equipment name for a service
     */
    public function validateEquipmentName(int $serviceId, string $name, ?int $excludeId = null): bool
    {
        return !$this->repository->equipmentNameExists($serviceId, $name, $excludeId);
    }

    /**
     * Get equipment summary for a service
     */
    public function getEquipmentSummary(int $serviceId): array
    {
        return $this->repository->getEquipmentSummary($serviceId);
    }

    /**
     * Get equipment count for a service
     */
    public function getEquipmentCount(int $serviceId): array
    {
        return $this->repository->getEquipmentCountForService($serviceId);
    }

    /**
     * Check if service has any equipment
     */
    public function serviceHasEquipment(int $serviceId): bool
    {
        $count = $this->repository->getEquipmentCountForService($serviceId);
        return $count['total_count'] > 0;
    }

    /**
     * Get equipment that client needs to provide
     */
    public function getClientProvidedEquipment(int $serviceId): Collection
    {
        return $this->repository->query()
                               ->where('chef_service_id', $serviceId)
                               ->where('is_included', false)
                               ->ordered()
                               ->get();
    }

    /**
     * Get equipment included in service
     */
    public function getIncludedEquipment(int $serviceId): Collection
    {
        return $this->repository->query()
                               ->where('chef_service_id', $serviceId)
                               ->where('is_included', true)
                               ->ordered()
                               ->get();
    }
}