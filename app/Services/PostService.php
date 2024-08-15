<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostFile;
use App\Services\Location\hasLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ramsey\Collection\Collection;

class PostService
{
    use hasLocation;

    public function delete(array $request): bool
    {
        $deleted = Post::where('id', $request['post_id'])
            ->delete();

        return (bool)$deleted;
    }

    public function updateText(array $request): bool
    {
        $updated = Post::where('id', $request['post_id'])
            ->update([
                'text' => $request['text']
            ]);

        return (bool)$updated;
    }

    public function updateFiles(array $request): bool
    {
        $files = PostFile::where('post_id', $request['post_id'])->get();
        if ($request['files']) {
            DB::beginTransaction();
            $images = [];
            $videos = [];
            try {
                foreach ($request['files'] as $file) {
                    $extension = $file->getClientOriginalExtension();
                    if (in_array($extension, ['jpeg', 'png', 'jpg', 'gif', 'svg'])) {
                        $images[] = $this->addImage($file, $request['post_id']);
                    } else $videos[] = $this->addVideo($file, $request['post_id']);
                }
                PostFile::where('post_id', $request['post_id'])->delete();
                DB::commit();
                $this->deleteFiles($files);
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                if ($files) {
                    $this->clearStorage($images, $videos);
                }
                logger($e);
                return false;
            }
        } else {
            $this->deleteFiles($files);
            PostFile::where('post_id', $request['post_id'])->delete();
            return true;
        }
    }

    public function create(array $request): bool
    {
        if ($request['text'] || $request['files']) {
            DB::beginTransaction();
            $files = $request['files'];
            $images = [];
            $videos = [];
            try {
                $location = $request['location'] ? $this->checkLocation($request['location']) : null;
                $createdPost = Post::create([
                    'user_id' => Auth::id(),
                    'repost_id' => $request['repost_id'],
                    'location' => $location,
                    'text' => $request['text']
                ]);
                if ($files && $createdPost) {
                    foreach ($files as $file) {
                        $extension = $file->getClientOriginalExtension();
                        if (in_array($extension, ['jpeg', 'png', 'jpg', 'gif', 'svg'])) {
                            $images[] = $this->addImage($file, $createdPost->id);
                        } else $videos[] = $this->addVideo($file, $createdPost->id);
                    }
                }
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                if ($files) {
                    $this->clearStorage($images, $videos);
                }
                logger($e);
                return false;
            }
        } else return false;
    }

    protected function clearStorage(array $images, array $videos): void
    {
        foreach ($images as $image) {
            $this->deleteImage($image);
        }

        foreach ($videos as $video) {
            $this->deleteVideo($video);
        }
    }

    protected function deleteFiles(\Illuminate\Database\Eloquent\Collection $files)
    {
        foreach ($files as $file) {
            if ($file->type == 'image') {
                $this->deleteImage($file->filename);
            } else {
                $this->deleteVideo($file->filename);
            }
        }
    }

    protected function addImage($file, int $postId): string
    {
        $fileName = basename(Storage::put('/private/images/posts', $file));
        PostFile::create([
            'post_id' => $postId,
            'type' => 'image',
            'filename' => $fileName,
        ]);
        return $fileName;
    }

    protected function addVideo($file, int $postId): string
    {
        $fileName = basename(Storage::put('/private/videos/posts', $file));
        PostFile::create([
            'post_id' => $postId,
            'type' => 'video',
            'filename' => $fileName,
        ]);
        return $fileName;
    }

    protected function deleteImage($name): void
    {
        Storage::delete('/private/images/posts/' . $name);
    }

    protected function deleteVideo($name): void
    {
        Storage::delete('/private/videos/posts/' . $name);
    }
}
