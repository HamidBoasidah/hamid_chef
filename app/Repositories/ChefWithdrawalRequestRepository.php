<?php

namespace App\Repositories;

use App\Models\ChefWithdrawalRequest;
use App\Repositories\Eloquent\BaseRepository;

class ChefWithdrawalRequestRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chef' => ['id', 'user_id', 'display_name'],
        'method' => ['id', 'name'],
    ];

    public function __construct(ChefWithdrawalRequest $model)
    {
        parent::__construct($model);
    }
}
