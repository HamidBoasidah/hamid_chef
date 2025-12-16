<?php

namespace App\Repositories;

use App\Models\Chef;
use App\Repositories\Eloquent\BaseRepository;

class ChefRepository extends BaseRepository
{
    protected array $defaultWith = [
        'user:id,first_name,last_name,email,phone_number',
        'services:id,chef_id,name,slug,service_type',
        'gallery:id,chef_id,image,caption',
        'wallet:id,chef_id,balance',
        'governorate:id,name_ar,name_en',
        'district:id,name_ar,name_en',
        'area:id,name_ar,name_en',
    ];

    public function __construct(Chef $model)
    {
        parent::__construct($model);
    }



    /*public function createOrUpdate(array $attributes)
    {
        // نحدد الحقل الذي نريد استخدامه لتفادي تكرار السجلات
        $uniqueKey = ['user_id' => $attributes['user_id']];

        // معالجة رفع الملفات أولاً
        $record = $this->model->where($uniqueKey)->first();

        $attributes = $this->handleFileUploads($attributes, $record);

        return $this->model->updateOrCreate($uniqueKey, $attributes);
    }*/

}
