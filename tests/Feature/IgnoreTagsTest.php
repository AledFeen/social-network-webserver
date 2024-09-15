<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostTag;
use App\Models\PreferredTag;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IgnoreTagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ignore(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag1->name
        ]);
        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag2->name
        ]);

        PreferredTag::factory()->create([
           'user_id' => $user1->id,
           'tag' => $tag1->name
        ]);

        $response = $this->actingAs($user1)->delete('/api/ignore', ['post_id' => $post->id]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('preferred_tags', 0);
    }
}
