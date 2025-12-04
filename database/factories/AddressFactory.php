<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use App\Models\Governorate;
use App\Models\District;
use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        $gov = Governorate::inRandomOrder()->first();
        $district = $gov ? District::where('governorate_id', $gov->id)->inRandomOrder()->first() : null;
        $area = $district ? Area::where('district_id', $district->id)->inRandomOrder()->first() : null;

        return [
            'user_id' => User::factory(),
            'label' => $this->faker->randomElement(['home', 'work', 'other']),
            'address' => $this->faker->address,
            'governorate_id' => $gov?->id,
            'district_id' => $district?->id,
            'area_id' => $area?->id,
            'street' => $this->faker->streetName(),
            'building_number' => $this->faker->numberBetween(1,200),
            'floor_number' => $this->faker->numberBetween(0,10),
            'apartment_number' => $this->faker->numberBetween(1,1000),
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
