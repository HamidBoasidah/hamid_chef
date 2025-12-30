<?php

namespace App\Services;

use App\Models\Chef;
use App\Models\Conversation;
use App\Repositories\ConversationRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ConversationService
{
    public function __construct(
        protected ConversationRepository $conversations
    ) {}

    public function query(?array $with = null): Builder
    {
        return $this->conversations->query($with);
    }

    public function find(int|string $id, ?array $with = null): Conversation
    {
        return $this->conversations->findOrFail($id, $with);
    }

    /**
     * Get conversations for the current authenticated principal (customer or chef)
     */
    public function getForCurrentPrincipal(?array $with = null): Builder
    {
        $user = Auth::user();
        // If the authenticated user is a chef owner, fetch their chef id
        $chefId = optional($user->chef)->id;

        if ($chefId) {
            return $this->conversations->forChef($chefId, $with);
        }

        return $this->conversations->forCustomer($user->id, $with);
    }

    /**
     * Ensure a conversation exists between current customer and given chef
     */
    public function ensureForCurrentUserAndChef(int $chefId): Conversation
    {
        $userId = Auth::id();
        return $this->conversations->firstOrCreate($userId, $chefId);
    }

    /**
     * Verify the authenticated principal is a participant of the conversation.
     */
    public function assertParticipant(Conversation $conversation): void
    {
        $user = Auth::user();
        $chefId = optional($user->chef)->id;

        $isParticipant = ($conversation->user_id === $user->id)
            || ($chefId && $conversation->chef_id === $chefId);

        if (!$isParticipant) {
            abort(403, __('messages.unauthorized'));
        }
    }
}
