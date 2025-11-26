<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefWithdrawalRequest extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'chef_id',
        'amount',
        'withdrawal_method_id',
        'status',
        'payment_details',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(WithdrawalMethod::class, 'withdrawal_method_id');
    }
}
