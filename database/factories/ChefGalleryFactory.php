<?php

namespace Database\Factories;

use App\Models\ChefGallery;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefGalleryFactory extends Factory
{
    protected $model = ChefGallery::class;

    public function definition()
    {
        return [
            'chef_id' => Chef::factory(),
            // Use placeholder filename (do not generate files during seeding)
            'image' => 'images/chef_gallery/' . $this->faker->uuid() . '.jpg',
            'caption' => $this->faker->sentence,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
