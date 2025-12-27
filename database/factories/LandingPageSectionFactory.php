<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LandingPageSection>
 */
class LandingPageSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'section_key' => $this->faker->unique()->slug(2),
            'title_ar' => $this->faker->sentence(3) . ' عربي',
            'title_en' => $this->faker->sentence(3),
            'description_ar' => $this->faker->paragraph() . ' عربي',
            'description_en' => $this->faker->paragraph(),
            'display_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
