<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentFile;
use App\Models\Post;
use App\Models\PostFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommentService
{
    public function create(array $request): bool
    {
        if ($request['text']) {
            DB::beginTransaction();
            $files = $request['files'];
            $images = [];
            try {
                $createdComment = Comment::create([
                    'post_id' => $request['post_id'],
                    'user_id' => Auth::id(),
                    'reply_id' => $request['reply_id'],
                    'text' => $request['text']
                ]);
                if($createdComment) {
                    foreach ($files as $file) {
                        $images[] = $this->addImage($file, $createdComment->id);
                    }
                    DB::commit();
                    return true;
                }
                return false;
            } catch (\Exception $e) {
                DB::rollBack();
                if ($files) {
                    $this->clearStorage($images);
                }
                logger($e);
                return false;
            }
        } else return false;
    }

    public function update(array $request) {
        $post = Post::where('id', $request['post_id'])->first();

        if($post->user_id == Auth::id()) {
            $updated = Comment::where('id', $request['comment_id'])->update([
                'text' => $request['text']
            ]);
            return (bool)$updated;
        }
        return false;
    }

    protected function addImage($file, int $commentId): string
    {
        $fileName = basename(Storage::put('/private/images/comments', $file));
        CommentFile::create([
            'comment_id' => $commentId,
            'filename' => $fileName,
        ]);
        return $fileName;
    }

    protected function deleteImage($name): void
    {
        Storage::delete('/private/images/comments/' . $name);
    }

    protected function clearStorage(array $images): void
    {
        foreach ($images as $image) {
            $this->deleteImage($image);
        }
    }
}
