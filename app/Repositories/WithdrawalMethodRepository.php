<?php

namespace App\Repositories;

use App\Models\WithdrawalMethod;
use App\Repositories\Eloquent\BaseRepository;

class WithdrawalMethodRepository extends BaseRepository
{
    protected array $defaultWith = [];

    public function __construct(WithdrawalMethod $model)
    {
        parent::__construct($model);
    }
}
