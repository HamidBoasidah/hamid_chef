<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function chefs(): BelongsToMany
    {
        return $this->belongsToMany(Chef::class, 'chef_categories', 'cuisine_id', 'chef_id');
    }

    public function chefCategories(): HasMany
    {
        return $this->hasMany(ChefCategory::class, 'cuisine_id');
    }
}
