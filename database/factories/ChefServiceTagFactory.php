<?php

namespace Database\Factories;

use App\Models\ChefServiceTag;
use App\Models\ChefService;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefServiceTagFactory extends Factory
{
    protected $model = ChefServiceTag::class;

    public function definition()
    {
        return [
            'chef_service_id' => ChefService::factory(),
            'tag_id' => Tag::factory(),
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
