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

class PreferredTagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get()
    {
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $user = User::factory()->create();

        $pf1 = PreferredTag::factory()->create([
            'user_id' => $user->id,
            'tag' => $tag1
        ]);

        $pf2 = PreferredTag::factory()->create([
            'user_id' => $user->id,
            'tag' => $tag2
        ]);

        $expectedData = [
            ['id' => $pf1->id, 'tag' => $pf1->tag, 'count' => $pf1->count],
            ['id' => $pf2->id, 'tag' => $pf2->tag, 'count' => $pf2->count],
        ];

        $response = $this->actingAs($user)->get("/api/preferredTags");

        $response->assertStatus(200)
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
    }

    public function test_add()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create();

        $response = $this->actingAs($user)->post('/api/preferredTag', ['text' => $tag1->name]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('preferred_tags', ['user_id' => $user->id]);
        $this->assertDatabaseCount('preferred_tags', 1);
    }

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

        $response = $this->actingAs($user1)->delete('/api/ignoreTag', ['post_id' => $post->id]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('preferred_tags', 0);
    }
}
