<?php

namespace App\Repositories;

use App\Models\Kyc;
use App\Repositories\Eloquent\BaseRepository;

class KycRepository extends BaseRepository
{
    protected array $defaultWith = [
        'user' => ['id', 'first_name', 'last_name', 'email'],
    ];

    public function __construct(Kyc $model)
    {
        parent::__construct($model);
    }
}
