<?php

namespace Tests\Feature;

use App\Models\Account;
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
    public function test_post_comment_observer_create(): void
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

    public function test_post_comment_observer_by_myself_create(): void
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

    public function test_notification_comment_get(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user_first->id
        ]);

        $comment1 = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user_second->id
        ]);

        $account_first = Account::where('user_id', $user_first->id)->first();
        $account_second = Account::where('user_id', $user_second->id)->first();

        $expectedData = [
            [
                'user' => ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image],
                'post_id' => $post->id,
                'text'=> $comment->text
            ],
            [
                'user' => ['id' => $user_second->id, 'name' => $user_second->name,'image' => $account_second->image],
                'post_id' => $post->id,
                'text' => $comment1->text
            ]
        ];

        $response = $this->actingAs($user)->get("/api/notification/comments");

        $response->assertStatus(200)
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
    }

    public function test_notification_comment_delete(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user_first->id
        ]);

        Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user_second->id
        ]);

        $response = $this->actingAs($user)->delete('/api/notification/comments');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseEmpty('notification_comments');
    }
}
