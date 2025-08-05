<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDeletion extends Model
{
    protected $fillable = [
        'user_id',
        'user',
        'action_by',
        'is_seen',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
