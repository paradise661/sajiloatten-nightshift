<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeSeen extends Model
{
    protected $fillable = ['notice_id', 'user_id'];
}
