<?php

namespace App\DTOs;

use App\Models\ChefServiceEquipment;

class ChefServiceEquipmentDTO extends BaseDTO
{
    public $id;
    public $chef_service_id;
    public $name;
    public $is_included;
    public $created_at;
    public $updated_at;
    public $chef_service;

    public static function fromModel(ChefServiceEquipment $equipment): self
    {
        $dto = new self();
        $dto->id = $equipment->id;
        $dto->chef_service_id = $equipment->chef_service_id;
        $dto->name = $equipment->name;
        $dto->is_included = $equipment->is_included;
        $dto->created_at = $equipment->created_at;
        $dto->updated_at = $equipment->updated_at;
        
        $dto->chef_service = $equipment->relationLoaded('chefService') && $equipment->chefService
            ? [
                'id' => $equipment->chefService->id,
                'name' => $equipment->chefService->name,
                'slug' => $equipment->chefService->slug,
            ]
            : null;

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_service_id' => $this->chef_service_id,
            'name' => $this->name,
            'is_included' => $this->is_included,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'chef_service' => $this->chef_service,
        ];
    }

    public function toIndexArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_included' => $this->is_included,
            'created_at' => $this->created_at,
        ];
    }

    public function toClientArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_included' => $this->is_included,
            'inclusion_status' => $this->is_included ? 'متضمن' : 'يوفره العميل',
            'inclusion_status_en' => $this->is_included ? 'Included' : 'Client Provided',
        ];
    }

    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'chef_service_id' => $this->chef_service_id,
            'name' => $this->name,
            'is_included' => $this->is_included,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'chef_service' => $this->chef_service,
            'inclusion_status' => $this->is_included ? 'متضمن' : 'يوفره العميل',
        ];
    }

    /**
     * Get equipment summary data
     */
    public static function equipmentSummary(array $summary): array
    {
        return [
            'total_count' => $summary['total_count'] ?? 0,
            'included_count' => $summary['included_count'] ?? 0,
            'client_provided_count' => $summary['client_provided_count'] ?? 0,
            'included_items' => $summary['included_items'] ?? [],
            'client_provided_items' => $summary['client_provided_items'] ?? [],
            'has_equipment' => ($summary['total_count'] ?? 0) > 0,
            'has_client_provided' => ($summary['client_provided_count'] ?? 0) > 0,
        ];
    }

    /**
     * Transform collection of equipment for client display
     */
    public static function collectionForClient($equipment): array
    {
        if (!$equipment || $equipment->isEmpty()) {
            return [
                'equipment' => [],
                'summary' => [
                    'total_count' => 0,
                    'included_count' => 0,
                    'client_provided_count' => 0,
                    'has_equipment' => false,
                    'has_client_provided' => false,
                    'message' => 'لا توجد أدوات محددة لهذه الخدمة',
                    'message_en' => 'No equipment specified for this service',
                ]
            ];
        }

        $equipmentArray = $equipment->map(function ($item) {
            return self::fromModel($item)->toClientArray();
        })->toArray();

        $includedCount = $equipment->where('is_included', true)->count();
        $clientProvidedCount = $equipment->where('is_included', false)->count();

        return [
            'equipment' => $equipmentArray,
            'summary' => [
                'total_count' => $equipment->count(),
                'included_count' => $includedCount,
                'client_provided_count' => $clientProvidedCount,
                'has_equipment' => true,
                'has_client_provided' => $clientProvidedCount > 0,
                'included_items' => $equipment->where('is_included', true)->pluck('name')->toArray(),
                'client_provided_items' => $equipment->where('is_included', false)->pluck('name')->toArray(),
            ]
        ];
    }

    /**
     * Transform collection of equipment for admin display
     */
    public static function collectionForAdmin($equipment): array
    {
        return $equipment->map(function ($item) {
            return self::fromModel($item)->toAdminArray();
        })->toArray();
    }

    /**
     * Transform collection of equipment for index display
     */
    public static function collectionForIndex($equipment): array
    {
        return $equipment->map(function ($item) {
            return self::fromModel($item)->toIndexArray();
        })->toArray();
    }
}