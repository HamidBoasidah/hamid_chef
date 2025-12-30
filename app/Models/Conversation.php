<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chef_id',
        'last_message_id',
        'last_message_at',
        'user_unread_count',
        'chef_unread_count',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'user_unread_count' => 'integer',
        'chef_unread_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
