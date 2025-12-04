<?php
namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition()
    {
        return [
            'name_ar' => $this->faker->citySuffix . ' عربي',
            'name_en' => $this->faker->city,
            'is_active' => true,
            // assign to an existing governorate if any, otherwise create one
            'governorate_id' => \App\Models\Governorate::inRandomOrder()->first()?->id ?? \App\Models\Governorate::factory()->create()->id,
            'created_by' => \App\Models\User::inRandomOrder()->first()?->id,
            'updated_by' => \App\Models\User::inRandomOrder()->first()?->id,
        ];
    }
}
