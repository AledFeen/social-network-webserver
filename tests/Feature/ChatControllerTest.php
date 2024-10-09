<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\MessageFile;
use App\Models\User;
use App\Models\Message;
use App\Models\UserChatLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_personal_chat(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/personal-chat', ['user_id' => $user1->id]);
        $response->assertStatus(201)
            ->assertJson(['success' => true]);;

        $this->assertDatabaseCount('chats', 1);
        $this->assertDatabaseCount('user_chat_links', 2);
    }

    public function test_create_personal_chat_existed(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);
        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);
        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);

        $response = $this->actingAs($user)->post('/api/personal-chat', ['user_id' => $user1->id]);
        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_get_chats()
    {
        $user = User::factory([
            'name' => 'authuser'
        ])->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        $link = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        $link1 = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);

        Message::factory()->create([
            'link_id' => $link1->id,
            'created_at' => now()->subMinutes(10),
            'updated_at' => now()->subMinutes(5),
        ]);

        Message::factory()->create([
            'link_id' => $link->id,
            'created_at' => now()->subMinutes(8),
            'updated_at' => now()->subMinutes(3),
        ]);

        $message = Message::factory()->create([
            'link_id' => $link1->id,
            'text' => 'merci',
            'created_at' => now()->subMinutes(5),
            'updated_at' => now()->subMinutes(2),
        ]);

        $chat1 = Chat::factory()->create([
            'type' => 'personal'
        ]);

        $link3 = UserChatLink::factory()->create([
            'chat_id' => $chat1->id,
            'user_id' => $user->id
        ]);

        $link4 = UserChatLink::factory()->create([
            'chat_id' => $chat1->id,
            'user_id' => $user2->id
        ]);

        Message::factory()->create([
            'link_id' => $link4->id,
            'text' => 'solos',
            'created_at' => now()->subMinutes(6),
            'updated_at' => now()->subMinutes(3),
        ]);

        $message1 = Message::factory()->create([
            'link_id' => $link3->id,
            'text' => null,
            'created_at' => now()->subMinutes(5),
            'updated_at' => now()->subMinutes(2),
        ]);

        $messageFile = MessageFile::factory()->create([
            'message_id' => $message1->id,
            'type' => 'audio',
            'name' => 'audio.mp3'
        ]);

        MessageFile::factory()->create([
            'message_id' => $message1->id,
            'type' => 'document'
        ]);

        MessageFile::factory()->create([
            'message_id' => $message1->id,
            'type' => 'photo'
        ]);

        $expectedData = [
            [
                'id' => $chat->id,
                'type' => $chat->type,
                'user' => [
                    'id' => $user1->id,
                    'name' => $user1->name,
                    'image' => 'default_avatar'
                ],
                'count_unread' => 2,
                'last_message' => [
                    "id" => $message->id,
                    "link_id" => $message->link_id,
                    "is_read" => false,
                    "text" => $message->text,
                    "created_at" => $message->created_at,
                    "updated_at" => $message->updated_at,
                    'user' => [
                        'id' => $user1->id,
                        'name' => $user1->name,
                        'image' => 'default_avatar'
                    ]
                ]
            ],
            [
                'id' => $chat1->id,
                'type' => $chat1->type,
                'user' => [
                    'id' => $user2->id,
                    'name' => $user2->name,
                    'image' => 'default_avatar'
                ],
                'count_unread' => 1,
                'last_message' => [
                    "id" => $message1->id,
                    "link_id" => $message1->link_id,
                    "is_read" => false,
                    "text" => $messageFile->name . '|2',
                    "created_at" => $message1->created_at,
                    "updated_at" => $message1->updated_at,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'image' => 'default_avatar'
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($user)->get('/api/chats');
        $response->assertStatus(200)
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);

        //dump($response->json());
    }

    public function test_get_chat_users()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        $link = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        $link1 = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);

        $expectedData = [
            [
                'id' => $link->id,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image' => 'default_avatar'
                ]
            ],
            [
                'id' => $link1->id,
                'user' => [
                    'id' => $user1->id,
                    'name' => $user1->name,
                    'image' => 'default_avatar'
                ]
            ]
        ];

        $this->actingAs($user)->get("/api/chat-users?chat_id={$chat->id}")
            ->assertStatus(200)
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
    }

    public function test_get_protected_chat_users()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);


        $this->actingAs($user2)->get("/api/chat-users?chat_id={$chat->id}")
            ->assertStatus(404);
    }

    public function test_get_messages()
    {
        $user = User::factory([
            'name' => 'authuser'
        ])->create();

        $user1 = User::factory()->create();
        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        $link = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        $link1 = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);

        $message = Message::factory()->create([
            'link_id' => $link1->id,
            'created_at' => now()->subMinutes(10),
            'updated_at' => now()->subMinutes(5),
        ]);

        $message1 = Message::factory()->create([
            'link_id' => $link->id,
            'created_at' => now()->subMinutes(8),
            'updated_at' => now()->subMinutes(3),
        ]);

        $message2 = Message::factory()->create([
            'link_id' => $link1->id,
            'text' => 'merci',
            'created_at' => now()->subMinutes(5),
            'updated_at' => now()->subMinutes(2),
        ]);

        $messageFile = MessageFile::factory()->create([
            'message_id' => $message->id,
            'type' => 'audio',
            'name' => 'audio.mp3'
        ]);

        $messageFile1 = MessageFile::factory()->create([
            'message_id' => $message->id,
            'type' => 'document',
            'name' => 'text.txt'
        ]);

        $messageFile2 = MessageFile::factory()->create([
            'message_id' => $message1->id,
            'type' => 'photo',
            'name' => 'image.png'
        ]);

        $expectedData = [
            [
                "id" => $message2->id,
                "link_id" => $message2->link_id,
                "is_read" => false,
                "text" => $message2->text,
                "created_at" => $message2->created_at,
                "updated_at" => $message2->updated_at,
                "files" => []
            ],
            [
                "id" => $message1->id,
                "link_id" => $message1->link_id,
                "is_read" => false,
                "text" => $message1->text,
                "created_at" => $message1->created_at,
                "updated_at" => $message1->updated_at,
                "files" => [
                    [
                        "id" => $messageFile2->id,
                        "message_id" => $messageFile2->message_id,
                        "type" => $messageFile2->type,
                        "filename" => $messageFile2->filename,
                        "name" => $messageFile2->name
                    ]
                ]
            ],
            [
                "id" => $message->id,
                "link_id" => $message->link_id,
                "is_read" => false,
                "text" => $message->text,
                "created_at" => $message->created_at,
                "updated_at" => $message->updated_at,
                "files" => [
                    [
                        "id" => $messageFile->id,
                        "message_id" => $messageFile->message_id,
                        "type" => $messageFile->type,
                        "filename" => $messageFile->filename,
                        "name" => $messageFile->name
                    ],
                    [
                        "id" => $messageFile1->id,
                        "message_id" => $messageFile1->message_id,
                        "type" => $messageFile1->type,
                        "filename" => $messageFile1->filename,
                        "name" => $messageFile1->name
                    ],

                ]
            ],

        ];

        $this->actingAs($user)->get("/api/messages?chat_id={$chat->id}&page_id=1")
            ->assertStatus(200)
            ->assertJsonFragment(['current_page' => 1])
            ->assertJsonFragment(['last_page' => 1])
            ->assertJsonFragment(['total' => 3])
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1])
            ->assertJsonFragment($expectedData[2]);
    }

    public function test_send_message_only_text(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);
        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);
        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);

        $response = $this->actingAs($user)->post('/api/message', ['chat_id' => $chat->id, 'text' => 'hello', 'files' => []]);
        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }


    public function test_send_message_with_files(): void
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        $link = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);

        $imageFile = \Illuminate\Http\UploadedFile::fake()->image('test-image.jpg');
        $videoFile = \Illuminate\Http\UploadedFile::fake()->create('test-video.mp4', 10000);
        $audioFile = \Illuminate\Http\UploadedFile::fake()->create('test-audio.mp3', 5000);
        $textFile = \Illuminate\Http\UploadedFile::fake()->create('test-file.txt', 100);


        $response = $this->actingAs($user)->post('/api/message', [
            'chat_id' => $chat->id,
            'text' => 'hello',
            'files' => [$imageFile, $videoFile, $audioFile, $textFile]
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $message = \App\Models\Message::where('link_id', $link->id)->first();
        $files = MessageFile::where('message_id', $message->id)->get();
        $this->deleteMessageFiles($files);
    }

    public function test_update_message_text()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        $link = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);

        $message = Message::factory()->create([
            'link_id' => $link->id
        ]);


        $response = $this->actingAs($user1)->put('/api/message', [
            'message_id' => $message->id,
            'text' => 'hello'
        ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);

        $response = $this->actingAs($user)->put('/api/message', [
            'message_id' => $message->id,
            'text' => 'hello'
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('messages', ['text' => 'hello']);

    }

    public function test_delete_message()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        $link = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);

        $message = Message::factory()->create([
            'link_id' => $link->id
        ]);

        MessageFile::factory()->create([
            'message_id' => $message->id
        ]);

        MessageFile::factory()->create([
            'message_id' => $message->id
        ]);

        $response = $this->actingAs($user1)->delete('/api/message', [
            'message_id' => $message->id,
        ]);

        $this->assertDatabaseCount('messages', 1);
        $this->assertDatabaseCount('message_files', 2);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);

        $response = $this->actingAs($user)->delete('/api/message', [
            'message_id' => $message->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseEmpty('messages');
        $this->assertDatabaseEmpty('message_files');

    }

    public function test_delete_message_with_files()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        $link = UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);


        $imageFile = \Illuminate\Http\UploadedFile::fake()->image('test-image.jpg');
        $videoFile = \Illuminate\Http\UploadedFile::fake()->create('test-video.mp4', 10000);
        $audioFile = \Illuminate\Http\UploadedFile::fake()->create('test-audio.mp3', 5000);
        $textFile = \Illuminate\Http\UploadedFile::fake()->create('test-file.txt', 100);


        $response = $this->actingAs($user)->post('/api/message', [
            'chat_id' => $chat->id,
            'text' => 'hello',
            'files' => [$imageFile, $videoFile, $audioFile, $textFile]
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $message = \App\Models\Message::where('link_id', $link->id)->first();

        $response = $this->actingAs($user)->delete('/api/message', [
            'message_id' => $message->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseEmpty('messages');
        $this->assertDatabaseEmpty('message_files');

    }

    public function test_delete_chat_with_files()
    {
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $chat = Chat::factory()->create([
            'type' => 'personal'
        ]);

        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id
        ]);

        UserChatLink::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user1->id
        ]);


        $imageFile = \Illuminate\Http\UploadedFile::fake()->image('test-image.jpg');
        $videoFile = \Illuminate\Http\UploadedFile::fake()->create('test-video.mp4', 10000);
        $audioFile = \Illuminate\Http\UploadedFile::fake()->create('test-audio.mp3', 5000);
        $textFile = \Illuminate\Http\UploadedFile::fake()->create('test-file.txt', 100);


        $this->actingAs($user)->post('/api/message', [
            'chat_id' => $chat->id,
            'text' => 'hello',
            'files' => [$imageFile, $videoFile, $audioFile, $textFile]
        ]);

        $this->actingAs($user1)->post('/api/message', [
            'chat_id' => $chat->id,
            'text' => 'hello',
            'files' => [$imageFile, $videoFile, $audioFile, $textFile]
        ]);

        $response = $this->actingAs($user2)->delete('/api/chat', [
            'chat_id' => $chat->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);

        $response = $this->actingAs($user)->delete('/api/chat', [
            'chat_id' => $chat->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseEmpty('chats');
        $this->assertDatabaseEmpty('user_chat_links');
        $this->assertDatabaseEmpty('messages');
        $this->assertDatabaseEmpty('message_files');
    }


    protected function deleteMessageFiles(\Illuminate\Database\Eloquent\Collection $files): void
    {
        foreach ($files as $file) {
            switch ($file->type) {
                case 'photo':
                    $this->deleteImage($file->filename);
                    break;

                case 'video':
                    $this->deleteVideo($file->filename);
                    break;

                case 'audio':
                    $this->deleteAudio($file->filename);
                    break;

                default:
                    $this->deleteFile($file->filename);
                    break;
            }
        }
    }

    protected function deleteImage($name): void
    {
        Storage::delete('/private/images/messages/' . $name);
    }

    protected function deleteVideo($name): void
    {
        Storage::delete('/private/videos/messages/' . $name);
    }

    protected function deleteAudio($name): void
    {
        Storage::delete('/private/audios/messages/' . $name);
    }

    protected function deleteFile($name): void
    {
        Storage::delete('/private/files/messages/' . $name);
    }
}
