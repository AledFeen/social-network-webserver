<?php

namespace Database\Factories;

use App\Models\UserChatLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use function Laravel\Prompts\text;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'link_id' => UserChatLink::factory(),
            'text' =>  substr(fake()->name(), 0, 512),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
