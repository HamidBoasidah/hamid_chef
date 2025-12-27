<?php

namespace App\Repositories;

use App\Models\LandingPageSection;
use App\Repositories\Eloquent\BaseRepository;

class LandingPageSectionRepository extends BaseRepository
{
    protected array $defaultWith = [
        'createdBy:id,first_name,last_name',
        'updatedBy:id,first_name,last_name',
    ];

    public function __construct(LandingPageSection $model)
    {
        parent::__construct($model);
    }

    public function getActiveOrdered()
    {
        return $this->model->active()->ordered()->get();
    }

    public function getBySectionKey(string $key)
    {
        return $this->model->where('section_key', $key)->first();
    }

    public function findByKey(string $key)
    {
        return $this->model->where('section_key', $key)->where('is_active', true)->first();
    }
}
