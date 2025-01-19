<?php

namespace App\Http\Controllers;

use App\Services\File\checkingPostFileAccess;
use App\Services\File\MustCheckPostFileAccess;


class FileController extends Controller implements MustCheckPostFileAccess
{
    use checkingPostFileAccess;

    protected function getImage($path): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if (!file_exists($path)) {
            abort(400);
        }

        return response()->file($path);
    }

    public function getAccountImage($filename)
    {
        $path = storage_path('/app/private/images/accounts/' . $filename);
        return $this->getImage($path);
    }

    public function getPostImage($filename)
    {
        if ($this->checkAccessPostFile($filename)) {
            $path = storage_path('/app/private/images/posts/' . $filename);
            return $this->getImage($path);
        } else {
            return response()->json(['error' => 'No rights'], 403);
        }
    }

}
