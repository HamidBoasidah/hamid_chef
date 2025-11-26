<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ChefService::class, 'chef_service_tags');
    }

    public function serviceTags(): HasMany
    {
        return $this->hasMany(ChefServiceTag::class);
    }
}
