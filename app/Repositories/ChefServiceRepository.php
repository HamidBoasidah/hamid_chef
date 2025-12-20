<?php

namespace App\Repositories;

use App\Models\ChefService;
use App\Repositories\Eloquent\BaseRepository;

class ChefServiceRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chef:id,name,logo',
        'images:id,chef_service_id,image,is_active',
        'tags:id,name,slug',
    ];

    public function __construct(ChefService $model)
    {
        parent::__construct($model);
    }
}