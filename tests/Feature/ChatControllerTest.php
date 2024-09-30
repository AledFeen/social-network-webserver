<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\MessageFile;
use App\Models\User;
use App\Models\UserChatLink;
use http\Message;
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
            ->assertJson(['success' => false]);;
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

        $response = $this->actingAs($user)->post('/api/send-message', ['chat_id' => $chat->id, 'text' => 'hello', 'files' => []]);
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

        // Создаем связь пользователей с чатом
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


        $response = $this->actingAs($user)->post('/api/send-message', [
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
