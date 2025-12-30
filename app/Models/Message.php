<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends BaseModel
{
    use HasFactory;

    /**
     * Avoid logging attachment binary fields and counters
     */
    protected array $dontLog = [
        'attachment',
        'attachment_mime',
        'attachment_size',
        'is_read',
    ];

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'content',
        'attachment',
        'attachment_mime',
        'attachment_size',
        'is_read',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Store attachments privately on local disk.
     */
    protected array $privateFiles = ['attachment'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
