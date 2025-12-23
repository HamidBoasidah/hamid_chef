<?php

namespace Database\Factories;

use App\Models\ChefService;
use App\Models\ChefServiceEquipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChefServiceEquipment>
 */
class ChefServiceEquipmentFactory extends Factory
{
    protected $model = ChefServiceEquipment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $equipmentNames = [
            'أطباق التقديم',
            'ملاعق التقديم',
            'أقدار الطبخ',
            'سكاكين المطبخ',
            'أكواب الشرب',
            'مناديل المائدة',
            'طاولة الطعام',
            'أدوات الشواء',
            'مقلاة كبيرة',
            'وعاء السلطة',
            'أطباق صغيرة',
            'ملاعق صغيرة',
            'شوك التقديم',
            'مبرد المشروبات',
            'صواني التقديم'
        ];

        return [
            'chef_service_id' => ChefService::factory(),
            'name' => $this->faker->randomElement($equipmentNames),
            'is_included' => $this->faker->boolean(60), // 60% chance of being included
        ];
    }

    /**
     * Indicate that the equipment is included in the service.
     */
    public function included(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_included' => true,
        ]);
    }

    /**
     * Indicate that the equipment is not included (client provided).
     */
    public function notIncluded(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_included' => false,
        ]);
    }

    /**
     * Create equipment with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create equipment for a specific service.
     */
    public function forService(ChefService $service): static
    {
        return $this->state(fn (array $attributes) => [
            'chef_service_id' => $service->id,
        ]);
    }
}