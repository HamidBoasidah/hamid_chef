<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefGallery extends BaseModel
{
    use HasFactory;

    protected $table = 'chef_gallery';

    protected $fillable = [
        'chef_id',
        'image',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }
}
