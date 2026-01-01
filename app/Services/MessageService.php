<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\MessageRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public function __construct(
        protected MessageRepository $messages,
        protected ConversationService $conversations
    ) {}

    public function query(?array $with = null): Builder
    {
        return $this->messages->query($with);
    }

    public function forConversation(int $conversationId, int $perPage = 20, ?array $with = null)
    {
        return $this->messages->forConversation($conversationId, $with)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Send a message in the conversation by the authenticated principal
     */
    public function send(Conversation $conversation, array $payload): Message
    {
        $this->conversations->assertParticipant($conversation);

        $user = Auth::user();
        $chefId = optional($user->chef)->id;

        $senderType = $chefId && $conversation->chef_id === $chefId ? 'chef' : 'user';
        $senderId = $senderType === 'chef' ? $chefId : $user->id;

        // Validate presence of content or attachment without causing undefined index
        $hasContent = array_key_exists('content', $payload) && (string)($payload['content'] ?? '') !== '';
        $hasAttachment = array_key_exists('attachment', $payload) && !empty($payload['attachment']);
        if (!$hasContent && !$hasAttachment) {
            abort(422, __('validation.custom.message.content_or_attachment_required'));
        }

        // Safely prepare attachment metadata
        /** @var UploadedFile|null $upload */
        $upload = ($hasAttachment && ($payload['attachment'] instanceof UploadedFile)) ? $payload['attachment'] : null;

        $data = [
            'conversation_id' => $conversation->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'content' => $hasContent ? $payload['content'] : null,
            'attachment' => $hasAttachment ? $payload['attachment'] : null,
            'attachment_mime' => $upload ? $upload->getClientMimeType() : ($payload['attachment_mime'] ?? null),
            'attachment_size' => $upload ? $upload->getSize() : ($payload['attachment_size'] ?? null),
        ];

        return DB::transaction(function () use ($data, $conversation, $senderType) {
            $message = $this->messages->create($data);

            // update conversation last message metadata and unread counters
            $conversation->last_message_id = $message->id;
            $conversation->last_message_at = $message->created_at;
            if ($senderType === 'user') {
                $conversation->chef_unread_count = ($conversation->chef_unread_count ?? 0) + 1;
            } else {
                $conversation->user_unread_count = ($conversation->user_unread_count ?? 0) + 1;
            }
            $conversation->save();

            return $message;
        });
    }

    /**
     * Mark messages as read for the authenticated side.
     */
    public function markAsRead(Conversation $conversation): Conversation
    {
        $this->conversations->assertParticipant($conversation);
        $user = Auth::user();
        $chefId = optional($user->chef)->id;

        if ($chefId && $conversation->chef_id === $chefId) {
            $conversation->chef_unread_count = 0;
        } else {
            $conversation->user_unread_count = 0;
        }
        $conversation->save();

        return $conversation;
    }
}
