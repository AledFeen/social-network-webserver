<?php

namespace App\Services\Location;

interface MustHaveLocation
{
    public function checkLocation(string $location): string;
}
