<?php

namespace App\DTOs;

use App\Models\Message;

class MessageDTO extends BaseDTO
{
    public $id;
    public $conversation_id;
    public $sender_type;
    public $sender_id;
    public $content;
    public $attachment_url;
    public $attachment_mime;
    public $attachment_size;
    public $is_read;
    public $created_at;

    public function __construct(
        $id,
        $conversation_id,
        $sender_type,
        $sender_id,
        $content,
        $attachment_url,
        $attachment_mime,
        $attachment_size,
        $is_read,
        $created_at
    ) {
        $this->id = $id;
        $this->conversation_id = $conversation_id;
        $this->sender_type = $sender_type;
        $this->sender_id = $sender_id;
        $this->content = $content;
        $this->attachment_url = $attachment_url;
        $this->attachment_mime = $attachment_mime;
        $this->attachment_size = $attachment_size;
        $this->is_read = (bool) $is_read;
        $this->created_at = $created_at;
    }

    public static function fromModel(Message $msg, ?string $downloadUrl = null): self
    {
        // If a precomputed download URL is provided, use it. Otherwise, use fileUrl for public disk.
        $attachmentUrl = $downloadUrl ?? null;
        if (!$attachmentUrl) {
            // If attachment is stored on public, BaseDTO::fileUrl will resolve it.
            $attachmentUrl = (new self(null,null,null,null,null,null,null,null,false,null))->fileUrl($msg->attachment);
        }

        return new self(
            $msg->id,
            $msg->conversation_id,
            $msg->sender_type,
            $msg->sender_id,
            $msg->content,
            $attachmentUrl,
            $msg->attachment_mime,
            $msg->attachment_size,
            $msg->is_read ?? false,
            $msg->created_at?->toDateTimeString()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_type' => $this->sender_type,
            'sender_id' => $this->sender_id,
            'content' => $this->content,
            'attachment_url' => $this->attachment_url,
            'attachment_mime' => $this->attachment_mime,
            'attachment_size' => $this->attachment_size,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
        ];
    }
}
