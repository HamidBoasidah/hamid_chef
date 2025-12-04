<?php

namespace App\Repositories;

use App\Models\ChefService;
use App\Repositories\Eloquent\BaseRepository;

class ChefServiceRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chef' => ['id', 'user_id', 'name', 'address'],
        'images' => ['id', 'chef_service_id', 'image'],
        'tags' => ['id', 'name', 'slug'],
    ];

    public function __construct(ChefService $model)
    {
        parent::__construct($model);
    }
}
