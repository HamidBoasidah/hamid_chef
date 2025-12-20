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

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active images ordered by creation date
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('created_at');
    }

    /**
     * Scope for ordered images by creation date
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(ChefService::class, 'chef_service_id');
    }
}
