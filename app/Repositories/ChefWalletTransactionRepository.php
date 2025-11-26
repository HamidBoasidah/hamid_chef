<?php

namespace App\Repositories;

use App\Models\ChefWalletTransaction;
use App\Repositories\Eloquent\BaseRepository;

class ChefWalletTransactionRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chef' => ['id', 'user_id', 'display_name'],
    ];

    public function __construct(ChefWalletTransaction $model)
    {
        parent::__construct($model);
    }
}
