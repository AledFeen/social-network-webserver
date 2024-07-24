<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacySettings extends Model
{
    public $timestamps = false;
    protected $guarded = false;
    use HasFactory;
}
