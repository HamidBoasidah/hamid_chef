<?php

namespace Database\Factories;

use App\Models\ChefServiceImage;
use App\Models\ChefService;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefServiceImageFactory extends Factory
{
    protected $model = ChefServiceImage::class;

    public function definition()
    {
        return [
            'chef_service_id' => ChefService::factory(),
            'image' => 'images/chef_service/' . $this->faker->uuid() . '.jpg',
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
