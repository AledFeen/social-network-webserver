<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\UserChatLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatService
{
    public function sendMessage(array $request): bool
    {
        if($request['files']) {
            $files = $request['files'];
        } else $files = [];

        if ($request['text'] || $files) {
            DB::beginTransaction();
            $images = [];
            $videos = [];
            $audios = [];
            $reserveFiles = [];
            try {
                $chatLink = UserChatLink::where('user_id', Auth::id())
                    ->where('chat_id', $request['chat_id'])->first();

                $createdMessage = Message::create([
                    'text' => $request['text'],
                    'link_id' => $chatLink->id
                ]);

                if ($files != null) {
                    foreach ($files as $file) {
                        $extension = $file->getClientOriginalExtension();

                        switch (true) {
                            case in_array($extension, ['jpeg', 'png', 'jpg', 'gif', 'svg']):
                                $images[] = $this->addImage($file, $createdMessage->id);
                                break;

                            case in_array($extension, ['mp4', 'mov', 'avi', 'mkv']):
                                $videos[] = $this->addVideo($file, $createdMessage->id);
                                break;

                            case in_array($extension, ['mp3', 'wav', 'ogg']):
                                $audios[] = $this->addAudio($file, $createdMessage->id);
                                break;

                            default:
                                $reserveFiles[] = $this->addFile($file, $createdMessage->id);
                                break;
                        }
                    }
                }
                DB::commit();
                return true;
            }
            catch (\Throwable $e) {
                DB::rollBack();
                if ($files) {
                    $this->clearStorage($images, $videos, $audios, $reserveFiles);
                }
                report($e);
                return false;
            }
        } else return false;
    }

    public function createPersonalChat(array $request): bool
    {
        $firstUser = Auth::id();
        $secondUser = $request['user_id'];

        if(!$this->checkPersonalChatExist($firstUser, $secondUser)) {
            DB::beginTransaction();
            try {
                $chat = Chat::create([
                    'type' => 'personal'
                ]);

                UserChatLink::create([
                    'user_id' => $firstUser,
                    'chat_id' => $chat->id
                ]);

                UserChatLink::create([
                    'user_id' => $secondUser,
                    'chat_id' => $chat->id
                ]);

                DB::commit();
                return true;
            }
            catch (\Exception $e) {
                DB::rollBack();
                logger($e);
                return false;
            }
        } else {
            return false;
        }
    }

    protected function checkPersonalChatExist(int $firstUser, int $secondUser): bool
    {
        $firstUserChats = UserChatLink::where('user_id', $firstUser)->pluck('chat_id');

        return (bool)UserChatLink::where('user_id', $secondUser)
            ->whereIn('chat_id', $firstUserChats)
            ->first();
    }

    protected function addFile($file, int $messageId): string
    {
        $fileName = basename(Storage::put('/private/files/messages', $file));
        MessageFile::create([
            'message_id' => $messageId,
            'type' => 'document',
            'filename' => $fileName,
        ]);
        return $fileName;
    }

    protected function addAudio($file, int $messageId): string
    {
        $fileName = basename(Storage::put('/private/audios/messages', $file));
        MessageFile::create([
            'message_id' => $messageId,
            'type' => 'audio',
            'filename' => $fileName,
        ]);
        return $fileName;
    }

    protected function addImage($file, int $messageId): string
    {
        $fileName = basename(Storage::put('/private/images/messages', $file));
        MessageFile::create([
            'message_id' => $messageId,
            'type' => 'photo',
            'filename' => $fileName,
        ]);
        return $fileName;
    }

    protected function addVideo($file, int $messageId): string
    {
        $fileName = basename(Storage::put('/private/videos/messages', $file));
        MessageFile::create([
            'message_id' => $messageId,
            'type' => 'video',
            'filename' => $fileName,
        ]);
        return $fileName;
    }

    protected function clearStorage(array $images, array $videos, array $audios, array $files): void
    {
        foreach ($images as $image) {
            $this->deleteImage($image);
        }

        foreach ($videos as $video) {
            $this->deleteVideo($video);
        }

        foreach ($audios as $audio) {
            $this->deleteAudio($audio);
        }

        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    protected function deleteMessageFiles(\Illuminate\Database\Eloquent\Collection $files): void
    {
        foreach ($files as $file) {
            if ($file->type == 'image') {
                $this->deleteImage($file->filename);
            } else {
                $this->deleteVideo($file->filename);
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
