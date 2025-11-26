<?php

namespace App\Repositories;

use App\Models\Chef;
use App\Repositories\Eloquent\BaseRepository;

class ChefRepository extends BaseRepository
{
    protected array $defaultWith = [
        'user' => ['id', 'first_name', 'last_name', 'email', 'phone_number'],
        'services' => ['id', 'chef_id', 'name', 'slug', 'service_type'],
        'gallery' => ['id', 'chef_id', 'image', 'caption'],
        'wallet' => ['id', 'chef_id', 'balance'],
    ];

    public function __construct(Chef $model)
    {
        parent::__construct($model);
    }
}
