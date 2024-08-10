<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BlockedUser;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlacklistCheckingTest extends TestCase
{
    use RefreshDatabase;


    public function test_get_subscribers_with_blocked_user(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();
        $user_third = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_first->id
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_second->id
        ]);

        BlockedUser::factory()->create([
           'user_id' => $user_second->id,
           'blocked_id' => $user_third->id
        ]);

        $account_first = Account::where('user_id', $user_first->id)->first();

        $expectedData = [
            ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image]
        ];

        $response = $this->actingAs($user_third)->get("/api/subscribers?user_id={$user->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 1])
            ->assertJsonFragment($expectedData)
            ->assertJsonMissing(['id' => $user_second->id]);
    }

    public function test_get_subscriptions_with_blocked_user(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();
        $user_third = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user_first->id,
            'follower_id' => $user->id
        ]);

        Subscription::factory()->create([
            'user_id' => $user_second->id,
            'follower_id' => $user->id
        ]);

        BlockedUser::factory()->create([
            'user_id' => $user_second->id,
            'blocked_id' => $user_third->id
        ]);

        $account_first = Account::where('user_id', $user_first->id)->first();

        $expectedData = [
            ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image],
        ];

        $response = $this->actingAs($user_third)->get("/api/subscriptions?user_id={$user->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 1])
            ->assertJsonFragment($expectedData)
            ->assertJsonMissing(['id' => $user_second->id]);
    }

}
