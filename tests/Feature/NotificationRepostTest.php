<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationRepostTest extends TestCase
{
    use RefreshDatabase;
    public function test_post_like_observer_create(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $repost = Post::factory()
            ->create([
                'user_id' => $user1->id,
                'repost_id' => $post->id
            ]);


        $this->assertDatabaseCount('notification_reposts', 1);

        $this->assertDatabaseHas('notification_reposts',
            [
                'user_id' => $user->id,
                'post_id' => $repost->id
            ]);
    }

    public function test_post_like_observer_by_myself_create(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        Post::factory()
            ->create([
                'user_id' => $user->id,
                'repost_id' => $post->id
            ]);


        $this->assertDatabaseCount('notification_reposts', 0);
    }

    public function test_notification_repost_get(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $repost = Post::factory()
            ->create([
                'user_id' => $user_first->id,
                'repost_id' => $post->id
            ]);

        $repost1 = Post::factory()
            ->create([
                'user_id' => $user_second->id,
                'repost_id' => $post->id
            ]);

        $account_first = Account::where('user_id', $user_first->id)->first();
        $account_second = Account::where('user_id', $user_second->id)->first();

        $expectedData = [
            [
                'user' => ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image],
                'post_id' => $repost->id,
                'repost_id' => $post->id
            ],
            [
                'user' => ['id' => $user_second->id, 'name' => $user_second->name,'image' => $account_second->image],
                'post_id' => $repost1->id,
                'repost_id' => $post->id
            ]
        ];

        $response = $this->actingAs($user)->get("/api/notification/reposts");

        $response->assertStatus(200)
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
    }

    public function test_notification_repost_delete(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        Post::factory()
            ->create([
                'user_id' => $user_first->id,
                'repost_id' => $post->id
            ]);

        Post::factory()
            ->create([
                'user_id' => $user_second->id,
                'repost_id' => $post->id
            ]);

        $response = $this->actingAs($user)->delete('/api/notification/reposts');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseEmpty('notification_reposts');
    }
}
