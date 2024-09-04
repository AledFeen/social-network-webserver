<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationLikeTest extends TestCase
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

        $like = PostLike::create([
            'user_id' => $user1->id,
            'post_id' => $post->id
        ]);

        $this->assertDatabaseCount('notification_likes', 1);

        $this->assertDatabaseHas('notification_likes',
            [
               'user_id' => $user->id,
               'like_id' => $like->id
            ]);
    }

    public function test_post_like_observer_by_myself_create(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        PostLike::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        $this->assertDatabaseCount('notification_likes', 0);
    }
}
