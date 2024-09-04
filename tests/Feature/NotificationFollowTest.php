<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationFollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_observer_create(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_first->id
        ]);

        $this->assertDatabaseCount('notification_follows', 1);
        $this->assertDatabaseHas('notification_follows', [
            'user_id' => $subscription->user_id,
            'follower_id' => $subscription->follower_id
        ]);
    }
}
