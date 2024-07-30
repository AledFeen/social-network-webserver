<?php

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    protected function getImage($path): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function getAccountImage($filename)
    {
        $path = storage_path('/app/private/images/accounts/' . $filename);
        return $this->getImage($path);
    }
}
