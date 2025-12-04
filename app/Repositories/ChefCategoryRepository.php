<?php

namespace App\Repositories;

use App\Models\ChefCategory;
use App\Repositories\Eloquent\BaseRepository;

class ChefCategoryRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chef' => ['id', 'user_id', 'name', 'address'],
        'category' => ['id', 'name', 'slug'],
    ];

    public function __construct(ChefCategory $model)
    {
        parent::__construct($model);
    }
}
