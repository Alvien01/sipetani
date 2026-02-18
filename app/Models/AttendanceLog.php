<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $table = 'attendance_logs';
    protected $fillable = [
        'siaga_alert_id',
        'personel_id',
        'role',
        'status',
        'keterangan',
        'attended_at'
    ];
     protected $casts = [
        'attended_at' => 'datetime'
    ];

    public function alert()
    {
        return $this->belongsTo(SiagaAlert::class, 'siaga_alert_id');
    }

    public function personel()
    {
        return $this->belongsTo(Personel::class);
    }

    public function scopeByAlert($query, $alertId)
    {
        return $query->where('siaga_alert_id', $alertId);
    }

    public function scopeHadir($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeTidakHadir($query)
    {
        return $query->where('status', 'tidak_hadir');
    }
}
