<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Location;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostTag;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostLikeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_post_like(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        PostLike::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        PostLike::create([
            'user_id' => $user1->id,
            'post_id' => $post->id
        ]);

        PostLike::create([
            'user_id' => $user2->id,
            'post_id' => $post->id
        ]);

        $account = Account::where('user_id', $user->id)->first();
        $account1 = Account::where('user_id', $user1->id)->first();
        $account2 = Account::where('user_id', $user2->id)->first();

        $expectedData = [
            ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
            ['id' => $user1->id, 'name' => $user1->name,'image' => $account1->image],
            ['id' => $user2->id, 'name' => $user2->name,'image' => $account2->image]
        ];

        $response = $this->actingAs($user)->get("/api/likes?post_id={$post->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 3])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1])
            ->assertJsonFragment($expectedData[2]);

    }
    public function test_like_post(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $response = $this->actingAs($user)->post('/api/like', ['post_id' => $post->id]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('post_likes', 1);
    }

    public function test_dislike_post(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        PostLike::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        $response = $this->actingAs($user)->post('/api/like', ['post_id' => $post->id]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseEmpty('post_likes');
    }

    public function test_preferred_tags(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag1->name
        ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag2->name
        ]);

        PostLike::create([
            'user_id' => $user1->id,
            'post_id' => $post->id
        ]);

        $this->assertDatabaseCount('preferred_tags', 2);
        $this->assertDatabaseHas('preferred_tags', [
           'user_id' => $user1->id,
           'tag' => $tag1->name
        ]);
        $this->assertDatabaseHas('preferred_tags', [
            'user_id' => $user1->id,
            'tag' => $tag2->name
        ]);
    }
}
