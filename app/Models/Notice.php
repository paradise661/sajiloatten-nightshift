<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'date',
        'time',
        'created_by',
        'file',
        'order',
        'status'
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_notices')->withTimestamps();
    }

    public function getExpoTokensAttribute(): array
    {
        $deptIds = $this->departments->pluck('id')->toArray();
        return User::whereNotNull('expo_token')
            ->where('status', 'Active')
            ->whereIn('department_id', $deptIds)
            ->pluck('expo_token')
            ->unique()
            ->toArray();
    }
}
