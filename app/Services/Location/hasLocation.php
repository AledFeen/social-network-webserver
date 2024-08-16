<?php

namespace App\Services\Location;

use App\Models\Location;

trait hasLocation
{
    public function checkLocation(string $location): string
    {
        if (!Location::where('name', $location)->first()) {
            Location::create(['name' => $location]);
            return $location;
        }
        return $location;
    }
}
