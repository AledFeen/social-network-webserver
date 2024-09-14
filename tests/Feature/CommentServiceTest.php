<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BlockedUser;
use App\Models\Comment;
use App\Models\CommentFile;
use App\Models\Location;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_comments(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $account = Account::where('user_id', $user->id)->first();
        $account1 = Account::where('user_id', $user1->id)->first();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $file = CommentFile::factory()->create([
            'comment_id' => $comment->id
        ]);

        $comment1 = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id
        ]);

        $file1 = CommentFile::factory()->create([
            'comment_id' => $comment1->id
        ]);

        $file2 = CommentFile::factory()->create([
            'comment_id' => $comment1->id
        ]);

        Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id,
            'reply_id' => $comment->id
        ]);

        $expectedData = [
            [
                'id' => $comment->id,
                'post_id' => $post->id,
                'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                'text' => $comment->text,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'hasReplies' => 1,
                'files' => [
                    [
                    'id' => $file->id,
                    'comment_id' => $file->comment_id,
                    'type' => $file->type,
                    'filename' => $file->filename
                    ]
                ]
            ],
            [
                'id' => $comment1->id,
                'post_id' => $post->id,
                'user' => ['id' => $user1->id, 'name' => $user1->name, 'image' => $account1->image],
                'text' => $comment1->text,
                'created_at' => $comment1->created_at,
                'updated_at' => $comment1->updated_at,
                'hasReplies' => 0,
                'files' => [
                    [
                        'id' => $file1->id,
                        'comment_id' => $file1->comment_id,
                        'type' => $file1->type,
                        'filename' => $file1->filename
                    ],
                    [
                        'id' => $file2->id,
                        'comment_id' => $file2->comment_id,
                        'type' => $file2->type,
                        'filename' => $file2->filename
                    ],
                ]
            ],
        ];

        $response = $this->actingAs($user)->get("/api/comments?post_id={$post->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 2])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
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

        Comment::factory()->create([
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
            ->assertJsonFragment($expectedData[0]);
    }

    public function test_get_comment_replies(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $account = Account::where('user_id', $user->id)->first();
        $account1 = Account::where('user_id', $user1->id)->first();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
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

        $file = CommentFile::factory()->create([
            'comment_id' => $comment->id
        ]);

        $comment1 = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id,
            'reply_id' => $mainComment->id
        ]);

        $file1 = CommentFile::factory()->create([
            'comment_id' => $comment1->id
        ]);

        $file2 = CommentFile::factory()->create([
            'comment_id' => $comment1->id
        ]);

        Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id,
            'reply_id' => $comment->id
        ]);

        $expectedData = [
            [
                'id' => $comment->id,
                'post_id' => $post->id,
                'user' => ['id' => $user->id, 'name' => $user->name, 'image' => $account->image],
                'text' => $comment->text,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'hasReplies' => 1,
                'files' => [
                    [
                        'id' => $file->id,
                        'comment_id' => $file->comment_id,
                        'type' => $file->type,
                        'filename' => $file->filename
                    ]
                ]
            ],
            [
                'id' => $comment1->id,
                'post_id' => $post->id,
                'user' => ['id' => $user1->id, 'name' => $user1->name, 'image' => $account1->image],
                'text' => $comment1->text,
                'created_at' => $comment1->created_at,
                'updated_at' => $comment1->updated_at,
                'hasReplies' => 0,
                'files' => [
                    [
                        'id' => $file1->id,
                        'comment_id' => $file1->comment_id,
                        'type' => $file1->type,
                        'filename' => $file1->filename
                    ],
                    [
                        'id' => $file2->id,
                        'comment_id' => $file2->comment_id,
                        'type' => $file2->type,
                        'filename' => $file2->filename
                    ],
                ]
            ],
        ];

        $response = $this->actingAs($user)->get("/api/comment-replies?reply_id={$mainComment->id}&page_id=1");

        $response->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 2])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
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

        Comment::factory()->create([
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
            ->assertJsonFragment($expectedData[0]);

    }

    public function test_leave_comment(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $data = [
            'post_id' => $post->id,
            'reply_id' => null,
            'text' => 'text',
            'files' => [
                UploadedFile::fake()->image('test_image.jpg'),
                UploadedFile::fake()->image('test_image1.jpg'),
            ]
        ];

        $response = $this->actingAs($user)->post('/api/comment', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', ['post_id' => $data['post_id'],
            'reply_id' => $data['reply_id'],
            'text' => $data['text']
        ]);
        $this->assertDatabaseCount('comment_files', 2);

        $comments = Comment::where('user_id', $user->id)
            ->with('files')
            ->first();
        $this->deleteCommentFiles($comments->files);
    }

    public function test_leave_comment_nullable_files(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $data = [
            'post_id' => $post->id,
            'reply_id' => null,
            'text' => 'text',
            'files' => [
            ]
        ];

        $response = $this->actingAs($user)->post('/api/comment', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    public function test_update_comment_text(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->put('/api/comment', ['comment_id' => $comment->id, 'text' => 'hello']);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', ['text' => 'hello']);
    }

    public function test_update_comment_invalid_user(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user1)->put('/api/comment', ['comment_id' => $comment->id, 'text' => 'hello']);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_delete_comment(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        CommentFile::factory()->create([
            'comment_id' => $comment->id
        ]);

        $response = $this->actingAs($user)->delete('/api/comment', ['comment_id' => $comment->id]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseEmpty('comments');
        $this->assertDatabaseEmpty('comment_files');
    }

    public function test_delete_comment_incorrect_user(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        CommentFile::factory()->create([
            'comment_id' => $comment->id
        ]);

        $response = $this->actingAs($user1)->delete('/api/comment', ['comment_id' => $comment->id]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseCount('comment_files', 1);
    }

    public function test_delete_comment_post_owner(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $location = Location::factory()->create();
        $post = Post::factory()
            ->create([
                'user_id' => $user->id,
                'location' => $location->name
            ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id
        ]);

        CommentFile::factory()->create([
            'comment_id' => $comment->id
        ]);

        $response = $this->actingAs($user)->delete('/api/comment', ['comment_id' => $comment->id]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    protected function deleteCommentFiles(\Illuminate\Database\Eloquent\Collection $images): void
    {
        foreach ($images as $image) {
            $this->deleteCommentImage($image);
        }
    }

    protected function deleteCommentImage($name): void
    {
        Storage::delete('/private/images/comments/' . $name);
    }
}
