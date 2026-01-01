<?php

namespace App\DTOs;

use App\Models\Conversation;

class ConversationDTO extends BaseDTO
{
    public $id;
    public $user_id;
    public $user_name;
    public $chef_id;
    public $chef_name;
    public $last_message_id;
    public $last_message_at;
    public $user_unread_count;
    public $chef_unread_count;
    public $is_active;
    public $last_message;

    public function __construct(
        $id,
        $user_id,
        $user_name,
        $chef_id,
        $chef_name,
        $last_message_id,
        $last_message_at,
        $user_unread_count,
        $chef_unread_count,
        $is_active,
        $last_message
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->user_name = $user_name;
        $this->chef_id = $chef_id;
        $this->chef_name = $chef_name;
        $this->last_message_id = $last_message_id;
        $this->last_message_at = $last_message_at;
        $this->user_unread_count = (int) $user_unread_count;
        $this->chef_unread_count = (int) $chef_unread_count;
        $this->is_active = (bool) $is_active;
        $this->last_message = $last_message;
    }

    public static function fromModel(Conversation $conv): self
    {
        $last = $conv->lastMessage ?? null;
        $lastMessageData = null;
        if ($last) {
            $downloadUrl = null;
            if ($last->attachment) {
                $downloadUrl = route('api.conversations.messages.attachment', [
                    'conversation' => $conv->id,
                    'message' => $last->id,
                ]);
            }
            $lastMessageData = [
                'id' => $last->id,
                'sender_type' => $last->sender_type,
                'sender_id' => $last->sender_id,
                'content' => $last->content,
                'attachment_url' => $downloadUrl,
                'attachment_mime' => $last->attachment_mime,
                'attachment_size' => $last->attachment_size,
                'is_read' => (bool) ($last->is_read ?? false),
                'created_at' => $last->created_at?->toDateTimeString(),
            ];
        }

        return new self(
            $conv->id,
            $conv->user_id,
            $conv->user?->name ?? null,
            $conv->chef_id,
            $conv->chef?->name ?? null,
            $conv->last_message_id,
            $conv->last_message_at?->toDateTimeString(),
            $conv->user_unread_count ?? 0,
            $conv->chef_unread_count ?? 0,
            $conv->is_active ?? true,
            $lastMessageData
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'chef_id' => $this->chef_id,
            'chef_name' => $this->chef_name,
            'last_message_id' => $this->last_message_id,
            'last_message_at' => $this->last_message_at,
            'user_unread_count' => $this->user_unread_count,
            'chef_unread_count' => $this->chef_unread_count,
            'is_active' => $this->is_active,
            'last_message' => $this->last_message,
        ];
    }
}
