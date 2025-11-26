<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kyc extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'rejected_reason',
        'verified_at',
        'full_name',
        'gender',
        'date_of_birth',
        'address',
        'document_type',
        'document_scan_copy',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
