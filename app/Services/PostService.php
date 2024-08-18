<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\PostTag;
use App\Models\Tag;
use App\Services\Location\hasLocation;
use App\Services\Location\MustHaveLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ramsey\Collection\Collection;

class PostService implements MustHaveLocation
{
    use hasLocation;

    public function delete(array $request): bool
    {
        $comments = Comment::where('post_id',  $request['post_id'])
            ->with('files')
            ->get();

        $commentFiles = $comments->flatMap(function ($comment) {
            return $comment->files;
        });
        $this->deleteCommentsImage($commentFiles);

        $postFiles = PostFile::where('post_id', $request['post_id'])->get();
        $this->deleteFiles($postFiles);

        $deleted = Post::where('id', $request['post_id'])
            ->delete();

        return (bool)$deleted;
    }

    public function updateTags(array $request): bool
    {
        $post = Post::where('id',  $request['post_id'])->first();

        if($post->user_id == Auth::id()) {

            if(PostTag::where('post_id', $request['post_id'])) {
                $this->checkTagExistence($request['tags']);
                foreach ($request['tags'] as $tag) {
                    PostTag::create([
                        'post_id' => $request['post_id'],
                        'tag' => $tag
                    ]);
                }
                return true;
            }
            return false;
        }
        return false;
    }

    public function updateText(array $request): bool
    {
        $post = Post::where('id', $request['post_id'])->first();

        if($post->user_id == Auth::id()) {
            $updated = $post->update([
                    'text' => $request['text']
                ]);

            return (bool)$updated;
        }
        return false;
    }

    public function updateFiles(array $request): bool
    {
        $files = PostFile::where('post_id', $request['post_id'])->get();
        $post = Post::where('id', $request['post_id'])->first();
        if($post->user_id == Auth::id()) {
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
        return false;
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
                $createdPost = $this->createPost($request['repost_id'], $location, $request['text']);
                if ($createdPost) {
                    $this->checkTagExistence($request['tags']);

                    foreach ($request['tags'] as $tag) {
                        PostTag::create([
                            'post_id' => $createdPost->id,
                            'tag' => $tag
                        ]);
                    }

                    foreach ($files as $file) {
                        $extension = $file->getClientOriginalExtension();
                        if (in_array($extension, ['jpeg', 'png', 'jpg', 'gif', 'svg'])) {
                            $images[] = $this->addImage($file, $createdPost->id);
                        } else $videos[] = $this->addVideo($file, $createdPost->id);
                    }

                    DB::commit();
                    return true;
                }
                return false;
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

    protected function deleteCommentsImage($files): void
    {
        foreach ($files as $file) {
            Storage::delete('/private/images/comments/' . $file->filename);
        }
    }

    protected function checkTagExistence(array $tags)
    {
        foreach ($tags as $tag) {
            if(!Tag::where('name', $tag)->first()) {
                Tag::create([
                   'name' => $tag
                ]);
            }
        }
    }

    protected function createPost(?int $repost_id, string $location, string $text)
    {
        return Post::create([
            'user_id' => Auth::id(),
            'repost_id' => $repost_id,
            'location' => $location,
            'text' => $text
        ]);
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
