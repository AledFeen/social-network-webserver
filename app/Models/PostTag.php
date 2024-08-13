<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    use HasFactory;
    protected $guarded = false;
    protected $primaryKey = ['post_id', 'tag'];
    protected $keyType = 'array';
    public $incrementing = false;
    public $timestamps = false;
}
