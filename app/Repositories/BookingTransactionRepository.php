<?php

namespace App\Repositories;

use App\Models\BookingTransaction;
use App\Repositories\Eloquent\BaseRepository;

class BookingTransactionRepository extends BaseRepository
{
    protected array $defaultWith = [
        'booking' => ['id', 'customer_id', 'chef_id', 'chef_service_id', 'date'],
    ];

    public function __construct(BookingTransaction $model)
    {
        parent::__construct($model);
    }
}
