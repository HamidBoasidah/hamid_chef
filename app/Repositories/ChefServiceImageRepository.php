<?php

namespace App\Repositories;

use App\Models\ChefServiceImage;
use App\Repositories\Eloquent\BaseRepository;

class ChefServiceImageRepository extends BaseRepository
{
    protected array $defaultWith = [
        'service' => ['id', 'chef_id', 'name', 'slug'],
    ];

    public function __construct(ChefServiceImage $model)
    {
        parent::__construct($model);
    }
}
