<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChefWallet extends BaseModel
{
    use HasFactory;

    protected $table = 'chef_wallet';

    protected $fillable = [
        'chef_id',
        'balance',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ChefWalletTransaction::class);
    }
}
