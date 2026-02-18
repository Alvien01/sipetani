<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiagaAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'status',
        'triggered_by',
        'level',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
    public function personelsHadir()
    {
        return $this->attendanceLogs()->hadir()->with('personel');
    }

    public function personelsTidakHadir()
    {
        return $this->attendanceLogs()->tidakHadir()->with('personel');
    }

    public function totalHadir()
    {
        return $this->attendanceLogs()->hadir()->count();
    }

    public function totalTidakHadir()
    {
        return $this->attendanceLogs()->tidakHadir()->count();
    }
}
