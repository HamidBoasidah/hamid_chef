<?php
namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFactory extends Factory
{
    protected $model = Area::class;

    public function definition()
    {
        return [
            'name_ar' => $this->faker->citySuffix . ' عربي',
            'name_en' => $this->faker->city,
            'is_active' => true,
            // pick an existing district or create one so the relation is valid
            'district_id' => \App\Models\District::inRandomOrder()->first()?->id ?? \App\Models\District::factory()->create()->id,
            'created_by' => \App\Models\User::inRandomOrder()->first()?->id,
            'updated_by' => \App\Models\User::inRandomOrder()->first()?->id,
        ];
    }
}
