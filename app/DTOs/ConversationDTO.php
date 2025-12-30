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
        $is_active
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
    }

    public static function fromModel(Conversation $conv): self
    {
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
            $conv->is_active ?? true
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
        ];
    }
}
