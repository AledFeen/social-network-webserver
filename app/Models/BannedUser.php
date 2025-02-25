<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannedUser extends Model
{
    use HasFactory;

    protected $guarded = false;
    protected $primaryKey = 'user_id';
    protected $keyType = 'int';
    public $incrementing = false;
}
