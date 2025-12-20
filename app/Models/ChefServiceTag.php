<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefServiceTag extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'chef_service_id',
        'tag_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ChefService::class, 'chef_service_id');
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
