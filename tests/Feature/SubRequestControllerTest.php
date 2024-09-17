<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\PrivacySettings;
use App\Models\SubscriptionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sub_request_user_with_private_account(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        PrivacySettings::where('user_id', $user_first->id)->update([
            'account_type' => 'private'
        ]);

        $response = $this->actingAs($user)->post('/api/subscribe-request', ['user_id' => $user_first->id]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    public function test_sub_request_user_with_public_account(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();


        $response = $this->actingAs($user)->post('/api/subscribe-request', ['user_id' => $user_first->id]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_sub_request_duplicate_account(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        PrivacySettings::where('user_id', $user_first->id)->update([
            'account_type' => 'private'
        ]);

        $response = $this->actingAs($user)->post('/api/subscribe-request', ['user_id' => $user_first->id]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $response1 = $this->actingAs($user)->post('/api/subscribe-request', ['user_id' => $user_first->id]);

        $response1->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_accept_sub_request(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        $request = SubscriptionRequest::factory()->create([
           'user_id' => $user->id,
           'follower_id' => $user_first->id
        ]);

        $response = $this->actingAs($user)->post('/api/accept-request', ['id' => $request->id]);
        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('subscriptions', 1);
        $this->assertDatabaseHas('subscriptions', [
           'user_id' => $user->id,
           'follower_id' => $user_first->id
        ]);
    }

    public function test_decline_sub_request(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        $request = SubscriptionRequest::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_first->id
        ]);

        $response = $this->actingAs($user)->post('/api/decline-request', ['id' => $request->id]);
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseEmpty('subscription_requests');
    }

    public function test_get_request(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        $request = SubscriptionRequest::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_first->id
        ]);

        $request1 = SubscriptionRequest::factory()->create([
            'user_id' => $user->id,
            'follower_id' => $user_second->id
        ]);

        $account_first = Account::where('user_id', $user_first->id)->first();
        $account_second = Account::where('user_id', $user_second->id)->first();

        $expectedData = [
            [
                'id' => $request->id,
                'user' => ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image],
                'created_at' => $request->created_at
            ],
            [
                'id' => $request1->id,
                'user' => ['id' => $user_second->id, 'name' => $user_second->name, 'image' => $account_second->image],
                'created_at' => $request1->created_at
            ]
        ];

        $response = $this->actingAs($user)->get("/api/subscription-requests?&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 2])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);

    }

}
