<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'location' => null,
            'repost_id' => null,
            'text' =>  substr(fake()->text(), 0, 512),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    public function withNoRepost(): static
    {
        return $this->state(fn(array $attributes) => [
            'repost_id' => null,
        ]);
    }
}
