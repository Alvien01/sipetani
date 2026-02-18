<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Personel extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'rank',
        'nrp',
        'position',
        'status',
        'fcm_token',
        'phone',
        'email',
        'password',
        'role_id',
        'last_online_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'last_online_at' => 'datetime',
    ];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getLastAttendanceAttribute()
    {
        return $this->attendanceLogs()
            ->latest()
            ->first();
    }
}
