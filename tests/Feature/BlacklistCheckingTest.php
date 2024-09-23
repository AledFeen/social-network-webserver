<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BlockedUser;
use App\Models\Comment;
use App\Models\Location;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\PostLike;
use App\Models\PostTag;
use App\Models\PreferredTag;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlacklistCheckingTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribe_banned(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        BlockedUser::factory()->create([
            'user_id' => $user_first->id,
            'blocked_id' => $user->id
        ]);

        $response = $this->actingAs($user)->post('/api/subscribe', ['user_id' => $user_first->id]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);

        $this->assertDatabaseMissing('subscriptions', [
            'user_id' => $user_first->id,
            'follower_id' => $user->id
        ]);
    }

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

    public function test_get_comments_with_banned()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $account = Account::where('user_id', $user->id)->first();

        BlockedUser::factory()->create([
            'user_id' => $user1,
            'blocked_id' => $user2
        ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $comment1 = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id
        ]);

        $expectedData = [
            [
                'id' => $comment->id,
                'post_id' => $post->id,
                'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                'text' => $comment->text,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'hasReplies' => 0,
                'files' => []
            ]
        ];

        $response = $this->actingAs($user2)->get("/api/comments?post_id={$post->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 1])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonMissing(['id' => $comment1->id]);
    }

    public function test_get_comment_replies_banned()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $account = Account::where('user_id', $user->id)->first();

        BlockedUser::factory()->create([
            'user_id' => $user1,
            'blocked_id' => $user2
        ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $mainComment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'reply_id' => $mainComment->id
        ]);

        $comment1 = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id,
            'reply_id' => $mainComment->id
        ]);

        $expectedData = [
            [
                'id' => $comment->id,
                'post_id' => $post->id,
                'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                'text' => $comment->text,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'hasReplies' => 0,
                'files' => []
            ]
        ];

        $response = $this->actingAs($user2)->get("/api/comment-replies?reply_id={$mainComment->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 1])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonMissing(['id' => $comment1->id]);
    }

    /*
    public function test_get_post_banned()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        BlockedUser::factory()->create([
            'user_id' => $user1,
            'blocked_id' => $user
        ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user1->id,
            ]);

        $response = $this->actingAs($user)->get("/api/post?post_id={$post->id}");

        $response->assertStatus(200)
            ->assertJsonMissing(['id' => $post->id]);
    }
    */

    public function test_get_posts_by_tag_banned(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $tag1 = Tag::factory()->create();
        $location = Location::factory()->create();

        BlockedUser::factory()->create([
            'user_id' => $user1,
            'blocked_id' => $user
        ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user1->id,
                'location' => $location->name
            ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag1->name
        ]);

        $response = $this->actingAs($user)->get("/api/posts-by-tag?tag={$tag1->name}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 0])
            ->assertJsonMissing(['id' => $post->id]);
    }
}
