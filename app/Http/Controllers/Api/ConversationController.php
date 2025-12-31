<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ConversationDTO;
use App\DTOs\MessageDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\StoreConversationByChefRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConversationController extends Controller
{
    use ExceptionHandler, SuccessResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List conversations for the authenticated principal (customer or chef)
     */
    public function index(Request $request, ConversationService $service)
    {
        $perPage = (int) $request->get('per_page', 10);
        $query = $service->getForCurrentPrincipal();
        $conversations = $query->latest()->paginate($perPage);

        $conversations->getCollection()->transform(function ($conv) {
            return ConversationDTO::fromModel($conv)->toArray();
        });

        return $this->collectionResponse($conversations, __('messages.list_success'));
    }

    /**
     * Ensure a conversation exists with the given chef id and optionally send an initial message
     */
    public function store(StoreConversationRequest $request, ConversationService $service, MessageService $messageService)
    {
        $data = $request->validated();

        // Ensure conversation
        $conversation = $service->ensureForCurrentUserAndChef($data['chef_id']);

        // Optional initial message
        if (!empty($data['content']) || !empty($data['attachment'])) {
            $messageService->send($conversation, $data);
        }

        // reload with default relations
        $conversation = $service->find($conversation->id);

        return $this->createdResponse(ConversationDTO::fromModel($conversation)->toArray(), __('messages.created_success'));
    }

    /**
     * Chef starts/ensures a conversation with a customer and optionally sends an initial message
     */
    public function storeByChef(StoreConversationByChefRequest $request, ConversationService $service, MessageService $messageService)
    {
        $data = $request->validated();

        // Ensure conversation by chef to user
        $conversation = $service->ensureForCurrentChefAndUser($data['user_id']);

        if (!empty($data['content']) || !empty($data['attachment'])) {
            $messageService->send($conversation, $data);
        }

        $conversation = $service->find($conversation->id);

        return $this->createdResponse(ConversationDTO::fromModel($conversation)->toArray(), __('messages.created_success'));
    }

    /**
     * List messages for a conversation
     */
    public function messages(Request $request, ConversationService $service, MessageService $messageService, $conversationId)
    {
        try {
            $conversation = $service->find($conversationId);
            $service->assertParticipant($conversation);

            $perPage = (int) $request->get('per_page', 20);
            $messages = $messageService->forConversation($conversation->id, $perPage);

            $messages->getCollection()->transform(function ($msg) use ($conversation) {
                // If attachment exists and is stored privately, expose a download route
                $downloadUrl = null;
                if ($msg->attachment) {
                    $downloadUrl = route('api.conversations.messages.attachment', [
                        'conversation' => $conversation->id,
                        'message' => $msg->id,
                    ]);
                }

                return MessageDTO::fromModel($msg, $downloadUrl)->toArray();
            });

            return $this->collectionResponse($messages, __('messages.list_success'));
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException(__('messages.not_found'));
        }
    }

    /**
     * Send a message in a conversation
     */
    public function sendMessage(StoreMessageRequest $request, ConversationService $service, MessageService $messageService, $conversationId)
    {
        try {
            $conversation = $service->find($conversationId);
            $service->assertParticipant($conversation);

            $data = $request->validated();
            $message = $messageService->send($conversation, $data);

            $downloadUrl = $message->attachment ? route('api.conversations.messages.attachment', [
                'conversation' => $conversation->id,
                'message' => $message->id,
            ]) : null;

            return $this->createdResponse(MessageDTO::fromModel($message, $downloadUrl)->toArray(), __('messages.created_success'));
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException(__('messages.not_found'));
        }
    }

    /**
     * Download an attachment for a message (private disk)
     */
    public function downloadAttachment(ConversationService $service, $conversationId, $messageId)
    {
        try {
            $conversation = $service->find($conversationId);
            $service->assertParticipant($conversation);

            $message = Message::query()->where('conversation_id', $conversation->id)->findOrFail($messageId);

            if (!$message->attachment) {
                $this->throwNotFoundException(__('messages.not_found'));
            }

            if (Storage::disk('local')->exists($message->attachment)) {
                $absolutePath = Storage::disk('local')->path($message->attachment);
                return response()->download($absolutePath);
            }

            // If stored on public (for any reason), fallback
            if (Storage::disk('public')->exists($message->attachment)) {
                $absolutePath = Storage::disk('public')->path($message->attachment);
                return response()->download($absolutePath);
            }

            $this->throwNotFoundException(__('messages.not_found'));
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException(__('messages.not_found'));
        }
    }

    /**
     * Mark messages in a conversation as read for current principal
     */
    public function markAsRead(ConversationService $service, MessageService $messageService, $conversationId)
    {
        try {
            $conversation = $service->find($conversationId);
            $service->assertParticipant($conversation);

            $updated = $messageService->markAsRead($conversation);
            return $this->updatedResponse(ConversationDTO::fromModel($updated)->toArray(), __('messages.updated_success'));
        } catch (ModelNotFoundException) {
            $this->throwNotFoundException(__('messages.not_found'));
        }
    }
}
