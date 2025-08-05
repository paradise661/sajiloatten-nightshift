<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'rating',
        'status',
        'is_seen'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
