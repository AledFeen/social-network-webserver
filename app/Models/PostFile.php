<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostFile extends Model
{
    use HasFactory;

    protected $guarded = false;
    public $timestamps = false;
}
