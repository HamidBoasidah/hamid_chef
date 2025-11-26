<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefCategory extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'chef_id',
        'cuisine_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'cuisine_id');
    }
}
