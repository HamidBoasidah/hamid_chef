<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'conversation_id' => function () {
                return Conversation::factory()->create()->id;
            },
            // placeholders; will be set in afterMaking
            'sender_type' => 'user',
            'sender_id' => 0,
            'content' => $this->faker->paragraph,
            'attachment' => null,
            'attachment_mime' => null,
            'attachment_size' => null,
            'is_read' => false,
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Message $message) {
            $conv = $message->conversation ?? Conversation::find($message->conversation_id);
            if (!$conv) {
                $conv = Conversation::factory()->create();
                $message->conversation_id = $conv->id;
            }

            // Randomly choose sender
            $senderType = $this->faker->randomElement(['user', 'chef']);
            $message->sender_type = $senderType;
            $message->sender_id = $senderType === 'user' ? $conv->user_id : $conv->chef_id;
        });
    }
}
