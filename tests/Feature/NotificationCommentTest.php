<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Location;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationCommentTest extends TestCase
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

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id
        ]);

        $this->assertDatabaseCount('notification_comments', 1);

        $this->assertDatabaseHas('notification_comments',
            [
                'user_id' => $user->id,
                'comment_id' => $comment->id
            ]);
    }

    public function test_post_like_observer_by_myself_create(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseCount('notification_comments', 0);
    }
}
