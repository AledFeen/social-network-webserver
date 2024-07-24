<?php

namespace App\Http\Controllers;

use App\Services\PrivacySettingsService;
use Illuminate\Http\Request;

class PrivacySettingsController extends Controller
{
    protected $service;
    public function __construct(PrivacySettingsService $service)
    {
        $this->service = $service;
    }


}
