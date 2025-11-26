<?php

namespace Database\Factories;

use App\Models\ChefCategory;
use App\Models\Chef;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefCategoryFactory extends Factory
{
    protected $model = ChefCategory::class;

    public function definition()
    {
        return [
            'chef_id' => Chef::factory(),
            'cuisine_id' => Category::factory(),
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
