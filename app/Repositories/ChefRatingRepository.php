<?php

namespace App\Repositories;

use App\Models\ChefRating;
use App\Repositories\Eloquent\BaseRepository;

class ChefRatingRepository extends BaseRepository
{
    protected array $defaultWith = [
        'booking' => ['id', 'customer_id', 'chef_id', 'chef_service_id', 'date'],
              'chef' => ['id', 'user_id', 'name', 'address'],
        'customer' => ['id', 'first_name', 'last_name'],
    ];

    public function __construct(ChefRating $model)
    {
        parent::__construct($model);
    }
}
