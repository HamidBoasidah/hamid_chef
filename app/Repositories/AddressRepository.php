<?php

namespace App\Repositories;

use App\Models\Address;
use App\Repositories\Eloquent\BaseRepository;

class AddressRepository extends BaseRepository
{
    protected array $defaultWith = [
        'user:id,first_name,last_name',
        'governorate:id,name_ar,name_en',
        'district:id,name_ar,name_en',
        'area:id,name_ar,name_en',
    ];

    public function __construct(Address $model)
    {
        parent::__construct($model);
    }

    // ما نحتاج نضيف أي شيء آخر هنا
}
