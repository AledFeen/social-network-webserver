<?php

namespace Tests\Feature;

use App\Http\Resources\PostDTO\MainPostDTOResource;
use App\Http\Resources\PostDTO\PostFileResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\UserDTO\UserDTOResource;
use App\Models\Account;
use App\Models\Comment;
use App\Models\CommentFile;
use App\Models\Location;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\PostLike;
use App\Models\PostTag;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_post(): void
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $repost = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name,
                'repost_id' => $post->id
            ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag1->name
        ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag2->name
        ]);

        $file = PostFile::factory()->create([
            'post_id' => $post->id
        ]);

        PostLike::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $account = Account::where('user_id', $user->id)->first();

        $expectedData = [
            [
                'id' => $post->id,
                'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                'repost_id' => null,
                'location' => $location->name,
                'text' => $post->text,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'repost_count' => 1,
                'like_count' => 1,
                'comment_count' => 1,
                'tags' => [
                    ['name' => $tag1->name],
                    ['name' => $tag2->name]
                ],
                'files' => [
                    [
                        'id' => $file->id,
                        'post_id' => $file->post_id,
                        'type' => $file->type,
                        'filename' => $file->filename
                    ]
                ],
                'main_post' => null
            ],
            [
                'id' => $repost->id,
                'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                'repost_id' => $post->id,
                'location' => $location->name,
                'text' => $repost->text,
                'created_at' => $repost->created_at,
                'updated_at' => $repost->updated_at,
                'repost_count' => 0,
                'like_count' => 0,
                'comment_count' => 0,
                'tags' => [],
                'files' => [],
                'main_post' => [
                    'id' => $post->id,
                    'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                    'location' => $location->name,
                    'text' => $post->text,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'tags' => [
                        ['name' => $tag1->name],
                        ['name' => $tag2->name]
                    ],
                    'files' => [
                        [
                            'id' => $file->id,
                            'post_id' => $file->post_id,
                            'type' => $file->type,
                            'filename' => $file->filename
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($user)->get("/api/posts?user_id={$user->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 2])
            ->assertJsonFragment($expectedData[1])
            ->assertJsonFragment($expectedData[0]);
    }

    public function test_get_reposts(): void
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $repost = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name,
                'repost_id' => $post->id
            ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag1->name
        ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag2->name
        ]);

        $file = PostFile::factory()->create([
            'post_id' => $post->id
        ]);

        PostLike::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $account = Account::where('user_id', $user->id)->first();

        $expectedData = [
            [
                'id' => $repost->id,
                'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                'repost_id' => $post->id,
                'location' => $location->name,
                'text' => $repost->text,
                'created_at' => $repost->created_at,
                'updated_at' => $repost->updated_at,
                'repost_count' => 0,
                'like_count' => 0,
                'comment_count' => 0,
                'tags' => [],
                'files' => [],
                'main_post' => [
                    'id' => $post->id,
                    'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                    'location' => $location->name,
                    'text' => $post->text,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'tags' => [
                        ['name' => $tag1->name],
                        ['name' => $tag2->name]
                    ],
                    'files' => [
                        [
                            'id' => $file->id,
                            'post_id' => $file->post_id,
                            'type' => $file->type,
                            'filename' => $file->filename
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($user)->get("/api/reposts?post_id={$post->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 1])
            ->assertJsonFragment($expectedData[0]);
    }

    public function test_create_post(): void
    {
        $user = User::factory()->create();

        $data = [
            'repost_id' => null,
            'location' => 'test location',
            'text' => 'This is a test post',
            'tags' => ['tag1', 'tag2'],
            'files' => [
                UploadedFile::fake()->image('test_image.jpg'),
                UploadedFile::fake()->create('test_video.mp4', 1000, 'video/mp4')
            ]
        ];

        $response = $this->actingAs($user)->post('/api/post', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'repost_id' => null,
            'location' => $data['location'],
            'text' => $data['text']
        ]);

        $this->assertDatabaseCount('post_tags', 2);
        $this->assertDatabaseHas('post_tags', [
            'tag' => 'tag1'
        ]);
        $this->assertDatabaseHas('post_tags', [
            'tag' => 'tag2'
        ]);

        $this->assertDatabaseCount('post_files', 2);
        $this->assertDatabaseHas('post_files', [
            'type' => 'image',
        ]);
        $this->assertDatabaseHas('post_files', [
            'type' => 'video',
        ]);

        $posts = Post::where('user_id', $user->id)
            ->with('files')
            ->first();

        $this->deletePostFiles($posts->files);
    }

    public function test_create_post_nullable_full(): void
    {
        $user = User::factory()->create();

        $data = [
            'repost_id' => null,
            'location' => null,
            'text' => null,
            'tags' => null,
            'files' => null
        ];

        $response = $this->actingAs($user)->post('/api/post', $data);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_create_post_nullable_with_text(): void
    {
        $user = User::factory()->create();

        $data = [
            'repost_id' => null,
            'location' => null,
            'text' => 'text',
            'tags' => null,
            'files' => null
        ];

        $response = $this->actingAs($user)->post('/api/post', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    public function test_create_post_nullable_with_files(): void
    {
        $user = User::factory()->create();
        $data = [
            'repost_id' => null,
            'location' => null,
            'text' => null,
            'tags' => null,
            'files' => [
                UploadedFile::fake()->image('test_image.jpg'),
                UploadedFile::fake()->create('test_video.mp4', 1000, 'video/mp4')
            ]
        ];

        $response = $this->actingAs($user)->post('/api/post', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $posts = Post::where('user_id', $user->id)
            ->with('files')
            ->first();
        $this->deletePostFiles($posts->files);
    }

    public function test_delete_post(): void
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag1->name
        ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag2->name
        ]);

        PostFile::factory()->create([
            'post_id' => $post->id
        ]);

        PostLike::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        CommentFile::factory()->create([
            'comment_id' => $comment->id
        ]);

        $response = $this->actingAs($user)->delete('/api/post', ['post_id' => $post->id]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseEmpty('posts');
        $this->assertDatabaseEmpty('comments');
        $this->assertDatabaseEmpty('comment_files');
        $this->assertDatabaseEmpty('post_tags');
        $this->assertDatabaseEmpty('post_files');
        $this->assertDatabaseEmpty('post_likes');
    }

    public function test_delete_post_invalid_user(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $response = $this->actingAs($user1)->delete('/api/post', ['post_id' => $post->id]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_update_post_text()
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $response = $this->actingAs($user)->put('/api/post', ['post_id' => $post->id, 'text' => 'updated text']);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'text' => 'updated text']);
    }

    public function test_update_post_text_nullable_with_files()
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        PostFile::factory()->create([
            'post_id' => $post->id
        ]);

        $response = $this->actingAs($user)->put('/api/post', ['post_id' => $post->id, 'text' => null]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'text' => null]);
    }

    public function test_update_post_text_nullable_without_files()
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $response = $this->actingAs($user)->put('/api/post', ['post_id' => $post->id, 'text' => null]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_update_post_text_invalid_user()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $response = $this->actingAs($user1)->put('/api/post', ['post_id' => $post->id, 'text' => 'updated text']);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_update_post_tags()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        PostTag::factory()->create([
            'post_id' => $post->id,
            'tag' => $tag1->name
        ]);

        $response = $this->actingAs($user)->put('/api/post-tags', ['post_id' => $post->id, 'tags' => ['tag2', 'tag3']]);
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('post_tags', 2);
        $this->assertDatabaseMissing('post_tags', ['tag' => $tag1->name]);
        $this->assertDatabaseHas('post_tags', ['tag' => 'tag2']);
        $this->assertDatabaseHas('post_tags', ['tag' => 'tag3']);
    }

    public function test_update_post_tags_nullable()
    {
        $user = User::factory()->create();
        Tag::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $response = $this->actingAs($user)->put('/api/post-tags', ['post_id' => $post->id, 'tags' => []]);
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseEmpty('post_tags');
    }

    public function test_update_post_tags_invalid_user()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $response = $this->actingAs($user1)->put('/api/post-tags', ['post_id' => $post->id, 'tags' => []]);
        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_update_post_files()
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        PostFile::factory()->create([
            'post_id' => $post->id
        ]);

        $files = [
            UploadedFile::fake()->image('test_image.jpg'),
            UploadedFile::fake()->image('test_image.jpg'),
        ];

        $response = $this->actingAs($user)->post('/api/post-files', ['post_id' => $post->id, 'files' => $files]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('post_files', 2);

        $posts = Post::where('user_id', $user->id)
            ->with('files')
            ->first();

        $this->deletePostFiles($posts->files);
    }

    public function test_update_post_files_nullable_with_text()
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        PostFile::factory()->create([
            'post_id' => $post->id
        ]);

        $response = $this->actingAs($user)->post('/api/post-files', ['post_id' => $post->id, 'files' => null]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseEmpty('post_files');
    }

    public function test_update_post_files_nullable_without_text()
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name,
                'text' => null
            ]);

        PostFile::factory()->create([
            'post_id' => $post->id
        ]);

        $response = $this->actingAs($user)->post('/api/post-files', ['post_id' => $post->id, 'files' => null]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('post_files', 1);
    }

    public function test_update_post_files_invalid_user()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        PostFile::factory()->create([
            'post_id' => $post->id
        ]);

        $response = $this->actingAs($user1)->post('/api/post-files', ['post_id' => $post->id, 'files' => []]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }


    protected function deletePostFiles(\Illuminate\Database\Eloquent\Collection $files): void
    {
        foreach ($files as $file) {
            if ($file->type == 'image') {
                $this->deletePostImage($file->filename);
            } else {
                $this->deletePostVideo($file->filename);
            }
        }
    }

    protected function deletePostImage($name): void
    {
        Storage::delete('/private/images/posts/' . $name);
    }

    protected function deletePostVideo($name): void
    {
        Storage::delete('/private/videos/posts/' . $name);
    }
}
