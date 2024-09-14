<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PrivacySettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTypeMiddlewareTest extends TestCase
{

    use RefreshDatabase;

    public function test_get_post_false(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
            'account_type' => 'private'
        ]);

        $response = $this->actingAs($user)->get("/api/posts?user_id={$user1->id}&page_id=1");

        $response->assertStatus(403);
    }

    public function test_get_subscribers_false()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
            'account_type' => 'private'
        ]);

        $response = $this->actingAs($user)->get("/api/subscribers?user_id={$user1->id}&page_id=1");
        $response->assertStatus(403);
    }

    public function test_get_subscriptions_false()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
            'account_type' => 'private'
        ]);

        $response = $this->actingAs($user)->get("/api/subscriptions?user_id={$user1->id}&page_id=1");
        $response->assertStatus(403);
    }
}
