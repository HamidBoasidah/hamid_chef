<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefServiceImage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'chef_service_id',
        'image',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ChefService::class, 'chef_service_id');
    }
}
