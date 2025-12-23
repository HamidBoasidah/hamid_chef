<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChefServiceEquipment extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chef_service_id',
        'name',
        'is_included',
    ];

    protected $casts = [
        'is_included' => 'boolean',
    ];

    /**
     * Get the chef service that owns this equipment
     */
    public function chefService(): BelongsTo
    {
        return $this->belongsTo(ChefService::class);
    }

    /**
     * Scope to get only included equipment
     */
    public function scopeIncluded($query)
    {
        return $query->where('is_included', true);
    }

    /**
     * Scope to get only not included equipment (client provided)
     */
    public function scopeNotIncluded($query)
    {
        return $query->where('is_included', false);
    }

    /**
     * Scope to order equipment by creation date (newest first)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to order equipment for client display (included first, then by creation date)
     */
    public function scopeClientOrdered($query)
    {
        return $query->orderBy('is_included', 'desc')->orderBy('created_at', 'desc');
    }
}