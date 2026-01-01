<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ConversationRepository extends BaseRepository
{
    /**
     * Default relations to load for conversations
     */
    protected array $defaultWith = [
        'user:id,first_name,last_name',
        'chef:id,name,user_id',
        'lastMessage:id,conversation_id,sender_type,sender_id,content,attachment,attachment_mime,attachment_size,is_read,created_at',
    ];

    public function __construct(Conversation $model)
    {
        parent::__construct($model);
    }

    /**
     * Query conversations for a customer (user)
     */
    public function forCustomer(int $userId, ?array $with = null): Builder
    {
        return $this->query($with)->where('user_id', $userId);
    }

    /**
     * Query conversations for a chef
     */
    public function forChef(int $chefId, ?array $with = null): Builder
    {
        return $this->query($with)->where('chef_id', $chefId);
    }

    /**
     * Find or create a conversation between a customer and chef.
     */
    public function firstOrCreate(int $userId, int $chefId): Conversation
    {
        $conversation = $this->query()->where('user_id', $userId)->where('chef_id', $chefId)->first();
        if ($conversation) {
            return $conversation;
        }

        return $this->create([
            'user_id' => $userId,
            'chef_id' => $chefId,
        ]);
    }
}
