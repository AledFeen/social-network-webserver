<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PrivacySettings;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CanRepostMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_subscribers_false(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
                'who_can_repost' => 'only_subscribers'
            ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user1->id,
            ]);

        $data = [
            'repost_id' => $post->id,
            'location' => 'test location',
            'text' => 'This is a test post',
            'tags' => [],
            'files' => []
        ];

        $response = $this->actingAs($user)->post('/api/post', $data);

        $response->assertStatus(403);
    }

    public function test_only_subscribers_true(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
            'who_can_repost' => 'only_subscribers'
        ]);

        Subscription::create([
           'user_id' => $user1->id,
           'follower_id' => $user->id
        ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user1->id,
            ]);

        $data = [
            'repost_id' => $post->id,
            'location' => 'test location',
            'text' => 'This is a test post',
            'tags' => [],
            'files' => []
        ];

        $response = $this->actingAs($user)->post('/api/post', $data);

        $response->assertStatus(201);
    }

    public function test_only_subscribers_none(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
            'who_can_repost' => 'none'
        ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user1->id,
            ]);

        $data = [
            'repost_id' => $post->id,
            'location' => 'test location',
            'text' => 'This is a test post',
            'tags' => [],
            'files' => []
        ];

        $response = $this->actingAs($user)->post('/api/post', $data);

        $response->assertStatus(403);
    }

}
