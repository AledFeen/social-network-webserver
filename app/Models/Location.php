<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $guarded = false;
    protected $primaryKey = 'name';
    public $incrementing = false;
    protected $keyType = 'string';
}
