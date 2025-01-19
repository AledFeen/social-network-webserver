<?php

namespace Tests\Feature;

use App\Models\BlockedUser;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\PrivacySettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_post_image_guarded(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        BlockedUser::factory([
            'user_id' => $user1->id,
            'blocked_id' => $user->id
        ])->create();

        $privacy = PrivacySettings::where('user_id', $user2->id)->first();
        $privacy->account_type = 'private';
        $privacy->save();

        $post = Post::factory()
            ->create([
                'user_id' => $user1->id
            ]);

        $file = PostFile::factory()->create([
            'post_id' => $post->id,
            'type' => 'image'
        ]);

        $post1 = Post::factory()
            ->create([
                'user_id' => $user2->id
            ]);

        $file1 = PostFile::factory()->create([
            'post_id' => $post1->id,
            'type' => 'image'
        ]);

        $post2 = Post::factory()
            ->create([
                'user_id' => $user3->id
            ]);

        $file2 = PostFile::factory()->create([
            'post_id' => $post2->id,
            'type' => 'image'
        ]);

        $response = $this->actingAs($user)->get("/api/post-image/$file->filename");
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get("/api/post-image/$file1->filename");
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get("/api/post-image/$file2->filename");
        $response->assertStatus(400);
    }
}
