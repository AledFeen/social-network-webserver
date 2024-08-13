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
            'location' => Location::factory()->create()->name,
            'repost_id' => Post::factory(),
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
