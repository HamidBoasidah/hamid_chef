<?php

namespace App\Repositories;

use App\Models\ChefServiceTag;
use App\Repositories\Eloquent\BaseRepository;

class ChefServiceTagRepository extends BaseRepository
{
    protected array $defaultWith = [
        'service' => ['id', 'chef_id', 'name', 'slug'],
        'tag' => ['id', 'name', 'slug'],
    ];

    public function __construct(ChefServiceTag $model)
    {
        parent::__construct($model);
    }
}
