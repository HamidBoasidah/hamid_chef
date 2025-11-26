<?php

namespace App\Repositories;

use App\Models\ChefGallery;
use App\Repositories\Eloquent\BaseRepository;

class ChefGalleryRepository extends BaseRepository
{
    protected array $defaultWith = [
        'chef' => ['id', 'user_id', 'display_name'],
    ];

    public function __construct(ChefGallery $model)
    {
        parent::__construct($model);
    }
}
