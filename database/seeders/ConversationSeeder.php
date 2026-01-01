<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Chef;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure some participants exist
        if (Chef::count() === 0) {
            Chef::factory()->count(5)->create();
        }
        if (User::where('user_type', 'customer')->count() === 0) {
            User::factory()->count(10)->customer()->create();
        }

        $customers = User::where('user_type', 'customer')->inRandomOrder()->take(10)->get();
        $chefs = Chef::inRandomOrder()->take(5)->get();

        // Create unique pairs up to min size
        $pairs = [];
        foreach ($customers as $customer) {
            $chef = $chefs->random();
            $key = $customer->id . ':' . $chef->id;
            if (!isset($pairs[$key])) {
                $pairs[$key] = [$customer->id, $chef->id];
            }
        }

        foreach ($pairs as [$customerId, $chefId]) {
            $conversation = Conversation::factory()->create([
                'user_id' => $customerId,
                'chef_id' => $chefId,
            ]);

            // Create a random number of messages
            $count = rand(5, 12);
            $userCount = 0;
            $chefCount = 0;
            $lastMessageId = null;
            $lastMessageAt = null;

            for ($i = 0; $i < $count; $i++) {
                /** @var Message $msg */
                $msg = Message::factory()->create([
                    'conversation_id' => $conversation->id,
                ]);

                $lastMessageId = $msg->id;
                $lastMessageAt = $msg->created_at;

                if ($msg->sender_type === 'user') {
                    $userCount++;
                } else {
                    $chefCount++;
                }
            }

            $conversation->last_message_id = $lastMessageId;
            $conversation->last_message_at = $lastMessageAt;
            // Use message counts as initial unread counters (approximation for demo)
            $conversation->user_unread_count = $chefCount;
            $conversation->chef_unread_count = $userCount;
            $conversation->save();
        }
    }
}
