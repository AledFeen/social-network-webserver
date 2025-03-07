<?php

namespace App\Http\Controllers;

use App\Services\File\checkingPostFileAccess;
use App\Services\File\MustCheckPostFileAccess;
use Illuminate\Support\Facades\Auth;


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
        if ($this->checkAccessPostFile($filename) || Auth::user()->role == 'admin') {
            $path = storage_path('/app/private/images/posts/' . $filename);
            return $this->getFile($path);
        } else {
            return response()->json(['error' => 'No rights'], 403);
        }
    }

    public function getPostVideo($filename)
    {
        if ($this->checkAccessPostFile($filename) || Auth::user()->role == 'admin') {
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


    public function getMessageImage($filename)
    {
        if ($this->checkAccessMessageFile($filename) || Auth::user()->role == 'admin') {
            $path = storage_path('/app/private/images/messages/' . $filename);
            return $this->getFile($path);
        } else {
            return response()->json(['error' => 'No rights'], 403);
        }

    }

    public function getMessageVideo($filename)
    {
        if ($this->checkAccessMessageFile($filename) || Auth::user()->role == 'admin') {
            $path = storage_path('/app/private/videos/messages/' . $filename);
            return $this->getFile($path);
        } else {
            return response()->json(['error' => 'No rights'], 403);
        }

    }

    public function getMessageAudio($filename)
    {
        if ($this->checkAccessMessageFile($filename) || Auth::user()->role == 'admin') {
            $path = storage_path('/app/private/audios/messages/' . $filename);
            return $this->getFile($path);
        } else {
            return response()->json(['error' => 'No rights'], 403);
        }

    }

    public function getMessageFile($filename)
    {
        if ($this->checkAccessMessageFile($filename) || Auth::user()->role == 'admin') {
            $path = storage_path('/app/private/files/messages/' . $filename);
            return $this->getFile($path);
        } else {
            return response()->json(['error' => 'No rights'], 403);
        }

    }
}
