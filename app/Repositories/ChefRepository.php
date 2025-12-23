<?php

namespace App\Repositories;

use App\Models\Chef;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ChefRepository extends BaseRepository
{
    protected array $defaultWith = [
        'user:id,first_name,last_name,email,phone_number',
        'services:id,chef_id,name,slug,service_type',
        'wallet:id,chef_id,balance',
        'governorate:id,name_ar,name_en',
        'district:id,name_ar,name_en',
        'area:id,name_ar,name_en',
        'categories:id,name,slug',
    ];

    public function __construct(Chef $model)
    {
        parent::__construct($model);
    }

    /**
     * Return a query for chefs that belong to the given category (cuisine_id on pivot)
     *
     * @param int $categoryId
     * @param array|null $with
     * @return Builder
     */
    public function queryByCategory(int $categoryId, ?array $with = null): Builder
    {
        return $this->makeQuery($with)->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }


}
