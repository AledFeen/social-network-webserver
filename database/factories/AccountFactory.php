<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
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
            'date_of_birth' => $this->faker->date(),
            'about_me' => $this->faker->sentence(),
            'image' => 'default_avatar',
            'real_name' => $this->faker->name(),
            'location' => Location::factory()->create()->name,
        ];
    }
}
