<?php

namespace App\Services\File;

interface MustCheckPostFileAccess
{
    function checkAccessPostFile(string $filename): bool;
}
