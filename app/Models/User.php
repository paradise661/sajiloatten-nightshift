<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'status',
        'password',
        'phone',
        'gender',
        'marital_status',
        'pan_number',
        'pan_photo',
        'employee_id',
        'image',
        'date_of_birth',
        'join_date',
        'resign_date',
        'designation',
        'branch_id',
        'department_id',
        'shift_id',
        'user_type',
        'expo_token',
        'otp',
        'location_preference',
        'order',
        'request_management',
        'platform',
        'device_flexible',
        'device_ids',
        'allow_attendance_request',
        'allow_leave_request',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        // 'remember_token',
        // 'expo_token'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('uploads/employee/' .  $value);
        }

        return asset('assets/images/profile.jpg');
    }
    public function attendanceRule()
    {
        return $this->hasOne(AttendanceRule::class);
    }

    public function bankDetails()
    {
        return $this->hasMany(EmployeeBankDetail::class);
    }

    public function defaultBank()
    {
        return $this->hasOne(EmployeeBankDetail::class)->where('is_default', true);
    }

    public function salarySetting()
    {
        return $this->hasOne(\App\Models\SalarySetting::class);
    }
}
