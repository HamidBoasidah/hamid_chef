<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefWalletTransaction extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'chef_id',
        'type',
        'amount',
        'balance',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }
}
