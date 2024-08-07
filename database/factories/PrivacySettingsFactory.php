<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrivacySettings>
 */
class PrivacySettingsFactory extends Factory
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
            'account_type' => 'public',
            'who_can_comment' => 'all',
            'who_can_repost' => 'all',
            'who_can_message' => 'all',
        ];
    }
}
