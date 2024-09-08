<?php

namespace Tests\Feature;

use App\Models\Account;
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

    public function test_notification_follow_get(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_first->id
        ]);

        Subscription::factory()->create([
            'user_id' => $user,
            'follower_id' => $user_second->id
        ]);

        $account_first = Account::where('user_id', $user_first->id)->first();
        $account_second = Account::where('user_id', $user_second->id)->first();

        $expectedData = [
            [
                'follower' => ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image],
            ],
            [
                'follower' => ['id' => $user_second->id, 'name' => $user_second->name,'image' => $account_second->image]
            ]
        ];

        $response = $this->actingAs($user)->get("/api/notification/followers");

        $response->assertStatus(200)
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
    }
}
