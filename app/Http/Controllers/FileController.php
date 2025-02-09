<?php

namespace App\Http\Controllers;

use App\Services\File\checkingPostFileAccess;
use App\Services\File\MustCheckPostFileAccess;


class FileController extends Controller implements MustCheckPostFileAccess
{
    use checkingPostFileAccess;

    protected function getFile($path): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if (!file_exists($path)) {
            abort(400);
        }

        return response()->file($path);
    }

    public function getAccountImage($filename)
    {
        $path = storage_path('/app/private/images/accounts/' . $filename);
        return $this->getFile($path);
    }

    public function getPostImage($filename)
    {
        if ($this->checkAccessPostFile($filename)) {
            $path = storage_path('/app/private/images/posts/' . $filename);
            return $this->getFile($path);
        } else {
            return response()->json(['error' => 'No rights'], 403);
        }
    }

    public function getPostVideo($filename)
    {
        if ($this->checkAccessPostFile($filename)) {
            $path = storage_path('/app/private/videos/posts/' . $filename);
            return $this->getFile($path);
        } else {
            return response()->json(['error' => 'No rights'], 403);
        }
    }

    public function getCommentImage($filename)
    {
        $path = storage_path('/app/private/images/comments/' . $filename);
        return $this->getFile($path);
    }

}
