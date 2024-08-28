<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostLikeServiceTest extends TestCase
{
    use RefreshDatabase;

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
}
