<?php

namespace App\Repositories;

use App\Models\District;
use App\Repositories\Eloquent\BaseRepository;

class DistrictRepository extends BaseRepository
{
    public function __construct(District $model)
    {
        parent::__construct($model);
    }
}
