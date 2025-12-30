<?php

namespace App\Repositories;

use App\Models\Message;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class MessageRepository extends BaseRepository
{
    /**
     * Default relations to load for messages
     */
    protected array $defaultWith = [
        'conversation:id,user_id,chef_id',
    ];

    public function __construct(Message $model)
    {
        parent::__construct($model);
    }

    /**
     * Query messages for a conversation
     */
    public function forConversation(int $conversationId, ?array $with = null): Builder
    {
        return $this->query($with)->where('conversation_id', $conversationId);
    }
}
