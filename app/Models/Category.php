<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'icon_path',
        'created_by',
        'updated_by',
    ];

    public function chefs(): BelongsToMany
    {
        return $this->belongsToMany(Chef::class, 'chef_categories', 'cuisine_id', 'chef_id')
            ->withPivot(['is_active', 'created_by', 'updated_by'])
            ->withTimestamps();
    }

    public function chefCategories(): HasMany
    {
        return $this->hasMany(ChefCategory::class, 'cuisine_id');
    }

    /**
     * Get the icon URL attribute.
     */
    public function getIconUrlAttribute(): ?string
    {
        return $this->icon_path ? Storage::url($this->icon_path) : null;
    }
}
