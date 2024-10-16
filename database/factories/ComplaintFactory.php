<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Message;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            /*
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'comment_id' => Comment::factory(),
            'message_id' => Message::factory(),
            */
            'text' => substr(fake()->name(), 0, 255),
            'status' => 'non-checked',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
