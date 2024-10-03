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
            ->assertJson(['success' => false]);;
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

        Message::factory()->create([
            'link_id' => $link1->id,
            'text' => 'curwa',
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

        Message::factory()->create([
            'link_id' => $link3->id,
            'created_at' => now()->subMinutes(5),
            'updated_at' => now()->subMinutes(2),
        ]);

        $response = $this->actingAs($user)->get('/api/chats');
        $response->assertStatus(200);
        dump($response->json());
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
           'message_id' => $message -> id
        ]);

        MessageFile::factory()->create([
            'message_id' => $message -> id
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
