<?php

namespace App\Repositories;

use App\Models\ChefWallet;
use App\Repositories\Eloquent\BaseRepository;

class ChefWalletRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chef' => ['id', 'user_id', 'display_name'],
        'transactions' => ['id', 'chef_id', 'type', 'amount', 'balance'],
    ];

    public function __construct(ChefWallet $model)
    {
        parent::__construct($model);
    }
}
