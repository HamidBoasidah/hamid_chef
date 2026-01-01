<?php

namespace App\Services;

use App\Models\Chef;
use App\Models\Conversation;
use App\Repositories\ConversationRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ValidationException;
use App\Exceptions\ForbiddenException;

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
     * Ensure a conversation exists between current chef and given customer
     */
    public function ensureForCurrentChefAndUser(int $userId): Conversation
    {
        $chefId = optional(Auth::user()->chef)->id;
        if (!$chefId) {
            // Return a clear validation error if the authenticated user doesn't have a Chef profile
            throw ValidationException::withMessages([
                'chef' => ['يجب إنشاء ملف الطاهي قبل بدء المحادثات.']
            ]);
        }
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
            // Use structured exception response instead of raw abort
            $message = __('messages.unauthorized');
            if ($message === 'messages.unauthorized') {
                $message = 'غير مصرح لك بالوصول لهذا المورد';
            }
            throw new ForbiddenException($message);
        }
    }

    /**
     * Ensure conversation between explicit participants (user_id & chef_id)
     */
    public function ensureByParticipants(int $userId, int $chefId): Conversation
    {
        return $this->conversations->firstOrCreate($userId, $chefId);
    }
}
