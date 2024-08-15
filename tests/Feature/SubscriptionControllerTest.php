<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribe_user(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/subscribe', ['user_id' => $user_first->id]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user_first->id,
            'follower_id' => $user->id
        ]);
    }

    public function test_unsubscribe(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        Subscription::factory()->create([
           'user_id' => $user_first->id,
           'follower_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete("/api/unsubscribe?user_id={$user_first->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $user_first->id, 'follower_id' => $user->id]);
    }

    public function test_delete_subscriber(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_first->id
        ]);

        $response = $this->actingAs($user)->delete("/api/delete-subscriber?user_id={$user_first->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $user->id, 'follower_id' => $user_first->id]);
    }

    public function test_get_subscribers(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_first->id
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_second->id
        ]);

        $account_first = Account::where('user_id', $user_first->id)->first();
        $account_second = Account::where('user_id', $user_second->id)->first();

        $expectedData = [
            ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image],
            ['id' => $user_second->id, 'name' => $user_second->name,'image' => $account_second->image]
        ];

        $response = $this->actingAs($user)->get("/api/subscribers?user_id={$user->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 2])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
    }

    public function test_get_subscriptions(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user_first->id,
            'follower_id' => $user->id
        ]);

        Subscription::factory()->create([
            'user_id' => $user_second->id,
            'follower_id' => $user->id
        ]);

        $account_first = Account::where('user_id', $user_first->id)->first();
        $account_second = Account::where('user_id', $user_second->id)->first();

        $expectedData = [
            ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image],
            ['id' => $user_second->id, 'name' => $user_second->name,'image' => $account_second->image]
        ];

        $response = $this->actingAs($user)->get("/api/subscriptions?user_id={$user->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 2])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
    }
}

