<?php

namespace App\Repositories;

use App\Models\Chef;
use App\Repositories\Eloquent\BaseRepository;

class ChefRepository extends BaseRepository
{
    protected array $defaultWith = [
        'user:id,first_name,last_name,email,phone_number',
        'services:id,chef_id,name,slug,service_type',
        'wallet:id,chef_id,balance',
        'governorate:id,name_ar,name_en',
        'district:id,name_ar,name_en',
        'area:id,name_ar,name_en',
        'categories:id,name,slug',
    ];

    public function __construct(Chef $model)
    {
        parent::__construct($model);
    }


}
