<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LandingPageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'image',
        'display_order',
        'additional_data',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'additional_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        // For now, assume it's always an admin since landing pages are managed by admins
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        // For now, assume it's always an admin since landing pages are managed by admins
        return $this->belongsTo(\App\Models\Admin::class, 'updated_by');
    }

     

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order');
    }
}
