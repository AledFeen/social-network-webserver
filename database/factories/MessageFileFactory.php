<?php

namespace Database\Factories;


use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessageFile>
 */
class MessageFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message_id' => Message::factory(),
            'type' => fake()->randomElement(['photo', 'video', 'audio', 'document']),
            'filename' => substr(fake()->text(), 0, 255),
            'name' => substr(fake()->text(),  0, 12) . fake()->randomElement(
                    ['.png', '.mp4', '.mp3', '.txt']),
        ];
    }
}
