<?php

namespace App\Repositories;

use App\Models\ChefServiceEquipment;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ChefServiceEquipmentRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chefService:id,name,slug',
    ];

    public function __construct(ChefServiceEquipment $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all equipment for a specific service
     */
    public function getEquipmentForService(int $serviceId): Collection
    {
        return $this->model->where('chef_service_id', $serviceId)
                          ->with($this->defaultWith)
                          ->ordered()
                          ->get();
    }

    /**
     * Get only active equipment for a specific service
     */
    public function getActiveEquipmentForService(int $serviceId): Collection
    {
        return $this->model->where('chef_service_id', $serviceId)
                          ->with($this->defaultWith)
                          ->clientOrdered()
                          ->get();
    }

    /**
     * Get equipment by name for a specific service
     */
    public function getEquipmentByName(int $serviceId, string $name): ?ChefServiceEquipment
    {
        return $this->model->where('chef_service_id', $serviceId)
                          ->where('name', $name)
                          ->with($this->defaultWith)
                          ->first();
    }

    /**
     * Check if equipment name exists for a service (excluding specific ID)
     */
    public function equipmentNameExists(int $serviceId, string $name, ?int $excludeId = null): bool
    {
        $query = $this->model->where('chef_service_id', $serviceId)
                            ->where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get equipment count for a service
     */
    public function getEquipmentCountForService(int $serviceId): array
    {
        $equipment = $this->model->where('chef_service_id', $serviceId)
                                ->get(['is_included']);

        return [
            'total_count' => $equipment->count(),
            'included_count' => $equipment->where('is_included', true)->count(),
            'client_provided_count' => $equipment->where('is_included', false)->count(),
        ];
    }

    /**
     * Duplicate equipment from one service to another
     */
    public function duplicateEquipmentToService(int $fromServiceId, int $toServiceId): bool
    {
        $sourceEquipment = $this->model->where('chef_service_id', $fromServiceId)
                                      ->get();

        if ($sourceEquipment->isEmpty()) {
            return false;
        }

        foreach ($sourceEquipment as $equipment) {
            $this->model->create([
                'chef_service_id' => $toServiceId,
                'name' => $equipment->name,
                'is_included' => $equipment->is_included,
            ]);
        }

        return true;
    }

    /**
     * Get equipment summary for a service
     */
    public function getEquipmentSummary(int $serviceId): array
    {
        $equipment = $this->model->where('chef_service_id', $serviceId)
                                ->get(['name', 'is_included']);

        $included = $equipment->where('is_included', true)->pluck('name')->toArray();
        $clientProvided = $equipment->where('is_included', false)->pluck('name')->toArray();

        return [
            'total_count' => $equipment->count(),
            'included_count' => count($included),
            'client_provided_count' => count($clientProvided),
            'included_items' => $included,
            'client_provided_items' => $clientProvided,
        ];
    }

    /**
     * Delete multiple equipment by IDs for a specific service
     */
    public function deleteEquipmentForService(int $serviceId, array $equipmentIds): int
    {
        return $this->model->where('chef_service_id', $serviceId)
                          ->whereIn('id', $equipmentIds)
                          ->delete();
    }

    /**
     * Update multiple equipment for a service
     */
    public function updateMultipleEquipment(array $equipmentData): bool
    {
        try {
            foreach ($equipmentData as $data) {
                if (isset($data['id'])) {
                    // Update existing equipment
                    $this->model->where('id', $data['id'])->update([
                        'name' => $data['name'],
                        'is_included' => $data['is_included'],
                    ]);
                } else {
                    // Create new equipment
                    $this->model->create([
                        'chef_service_id' => $data['chef_service_id'],
                        'name' => $data['name'],
                        'is_included' => $data['is_included'],
                    ]);
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}