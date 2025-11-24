<?php

namespace App\Repositories;

use App\Models\Area;
use App\Repositories\Eloquent\BaseRepository;

class AreaRepository extends BaseRepository
{
    public function __construct(Area $model)
    {
        parent::__construct($model);
    }
}
