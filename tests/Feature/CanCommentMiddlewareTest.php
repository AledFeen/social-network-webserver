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

class CanCommentMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_subscribers_true(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
            'who_can_comment' => 'only_subscribers'
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
            'post_id' => $post->id,
            'reply_id' => null,
            'text' => 'text',
            'files' => []
        ];

        $response = $this->actingAs($user)->post('/api/comment', $data);

        $response->assertStatus(201);
    }

    public function test_only_subscribers_false(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
            'who_can_comment' => 'only_subscribers'
        ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user1->id,
            ]);

        $data = [
            'post_id' => $post->id,
            'reply_id' => null,
            'text' => 'text',
            'files' => []
        ];

        $response = $this->actingAs($user)->post('/api/comment', $data);

        $response->assertStatus(403);
    }

    public function test_only_subscribers_none(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        PrivacySettings::where('user_id', $user1->id)->update([
            'who_can_comment' => 'none'
        ]);

        $post = Post::factory()
            ->create([
                'user_id' => $user1->id,
            ]);

        $data = [
            'post_id' => $post->id,
            'reply_id' => null,
            'text' => 'text',
            'files' => []
        ];

        $response = $this->actingAs($user)->post('/api/comment', $data);

        $response->assertStatus(403);
    }
}
