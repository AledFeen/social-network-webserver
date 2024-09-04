<?php

namespace Tests\Feature;

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
}
