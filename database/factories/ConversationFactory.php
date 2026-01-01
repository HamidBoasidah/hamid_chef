<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'user_id' => function () {
                // Prefer existing customers; create one if none
                $user = User::where('user_type', 'customer')->inRandomOrder()->first();
                return $user?->id ?? User::factory()->customer()->create()->id;
            },
            'chef_id' => function () {
                $chef = Chef::inRandomOrder()->first();
                return $chef?->id ?? Chef::factory()->create()->id;
            },
            'last_message_id' => null,
            'last_message_at' => null,
            'user_unread_count' => 0,
            'chef_unread_count' => 0,
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
