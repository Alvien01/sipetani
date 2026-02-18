<?php

namespace App\Http\Controllers;

use App\Models\Personel;
use App\Models\SiagaAlert;
use App\Models\AttendanceLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class AlarmController extends Controller
{
    public function trigger(Request $request)
    {
        $request->validate([
            'level' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $activeAlert = SiagaAlert::where('status', 'active')->first();
        if ($activeAlert) {
            return redirect()->back()->with('error', 'Siaga already active!');
        }

        DB::beginTransaction();

        try {
            // Buat alarm baru
            $alert = SiagaAlert::create([
                'level' => $request->level,
                'title' => $request->title,
                'message' => $request->message,
                'status' => 'active',
                'triggered_by' => Auth::guard('personel')->user()->name,
                'started_at' => now(),
            ]);

            Personel::whereHas('role', function($q) {
                $q->where('name', '!=', 'komandan');
            })->update(['status' => 'Siaga']);

            $personels = Personel::whereHas('role', function($q) {
                $q->where('name', '!=', 'komandan');
            })->get();

            foreach ($personels as $personel) {
                AttendanceLog::create([
                    'siaga_alert_id' => $alert->id,
                    'personel_id' => $personel->id,
                    'role' => $personel->role->name,
                    'status' => 'tidak_hadir',
                    'keterangan' => 'Belum hadir'
                ]);
            }

            // Kirim notifikasi FCM
            $fcmSuccess = false;
            try {
                $messaging = app('firebase.messaging');
                $topic = 'siaga-alerts';

                $notification = Notification::create(
                    $request->title . ' - SIAGA TK ' . $request->level,
                    $request->message
                );

                $message = CloudMessage::withTarget('topic', $topic)
                    ->withNotification($notification)
                    ->withData([
                        'status' => 'active',
                        'alert_id' => $alert->id,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ])
                    ->withAndroidConfig([
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'siaga_alerts',
                            'default_sound' => true,
                            'default_vibrate_timings' => true,
                            'notification_priority' => 'PRIORITY_MAX',
                            'visibility' => 'PUBLIC',
                        ],
                    ]);

                $messaging->send($message);
                $fcmSuccess = true;
                Log::info('FCM notification sent successfully');

            } catch (\Throwable $e) {
                Log::error('FCM Send Error: ' . $e->getMessage());
            }

            try {
                $smsService = new \App\Services\SmsService();
                $smsMessage = "🚨 SIAGA TK {$request->level} AKTIF!\n\n{$request->title}\n{$request->message}\n\nSegera konfirmasi kehadiran Anda.";
                $smsSent = 0;
                $offlineThreshold = now()->subMinutes(2);

                foreach ($personels as $personel) {
                    $isOffline = $personel->last_online_at === null || $personel->last_online_at < $offlineThreshold;
                    $hasNoToken = empty($personel->fcm_token);

                    if (($isOffline || $hasNoToken) && !empty($personel->phone)) {
                        $result = $smsService->send($personel->phone, $smsMessage);
                        if ($result) {
                            $smsSent++;
                            Log::info("SMS sent to {$personel->name} ({$personel->phone}) - Reason: " . ($hasNoToken ? 'No Token' : 'Offline'));
                        }
                    }
                }

                if ($smsSent > 0) {
                    Log::info("Total SMS sent: {$smsSent}");
                }

            } catch (\Throwable $e) {
                Log::error('SMS Send Error: ' . $e->getMessage());
            }

            DB::commit();

            $notificationMethod = $fcmSuccess ? 'Push Notification' : 'SMS';
            return redirect()->back()->with('success', "Alarm Siaga Diaktifkan & Notifikasi Terkirim via {$notificationMethod}!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trigger Alarm Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengaktifkan alarm: ' . $e->getMessage());
        }
    }

    public function attend(Request $request)
    {
        $user = Auth::guard('personel')->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $alert = SiagaAlert::where('status', 'active')->first();

        if (!$alert) {
            return redirect()->route('alert.view')->with('error', 'Tidak ada alarm aktif!');
        }

        DB::beginTransaction();

        try {
            // Update status personel
            $personel = Personel::where('id', $user->id)->first();
            $personel->status = 'Terkonfirmasi';
            $personel->save();

            // Update log kehadiran
            $attendanceLog = AttendanceLog::where('siaga_alert_id', $alert->id)
                ->where('personel_id', $personel->id)
                ->first();

            if ($attendanceLog) {
                $attendanceLog->update([
                    'status' => 'hadir',
                    'keterangan' => 'Hadir sesuai panggilan',
                    'attended_at' => now()
                ]);

                DB::commit();

                Log::info("Personel {$user->name} (NRP: {$user->nrp}) confirmed attendance");

                return redirect()->route('alert.view')->with('success', 'Kehadiran Anda telah dikonfirmasi!');
            } else {
                DB::rollBack();
                Log::error("Attendance log not found for personel {$user->id} in alert {$alert->id}");
                return redirect()->route('alert.view')->with('error', 'Log kehadiran tidak ditemukan!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Attendance Error: ' . $e->getMessage());
            return redirect()->route('alert.view')->with('error', 'Gagal mencatat kehadiran: ' . $e->getMessage());
        }
    }

    public function stop(Request $request)
    {
        $activeAlert = SiagaAlert::where('status', 'active')->first();

        if ($activeAlert) {
            DB::beginTransaction();

            try {
                $activeAlert->update([
                    'status' => 'resolved',
                    'ended_at' => now(),
                ]);

                // Update status personel (kecuali komandan) kembali ke Tersedia
                Personel::whereHas('role', function($q) {
                    $q->where('name', '!=', 'komandan');
                })->update(['status' => 'Tersedia']);

                // Kirim notifikasi alarm selesai
                try {
                    $messaging = app('firebase.messaging');
                    $topic = 'siaga-alerts';

                    $notification = Notification::create(
                        'SIAGA SELESAI',
                        'Alarm siaga telah dihentikan. Status: Resolved'
                    );

                    $message = CloudMessage::withTarget('topic', $topic)
                        ->withNotification($notification)
                        ->withData([
                            'status' => 'resolved',
                            'alert_id' => $activeAlert->id,
                        ]);

                    $messaging->send($message);
                    Log::info('FCM stop notification sent successfully');

                } catch (\Throwable $e) {
                    Log::error('FCM Stop Send Error: ' . $e->getMessage());
                }

                DB::commit();

                return redirect()->back()->with('success', 'Alarm Siaga Dimatikan.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Stop Alarm Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal mematikan alarm: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Tidak ada alarm aktif!');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        try {
            $user = Auth::guard('personel')->user();
            if ($user) {
                $personel = Personel::where('id', $user->id)->first();
                $personel->fcm_token = $request->token;
                $personel->save();
            }
            $messaging = app('firebase.messaging');
            $messaging->subscribeToTopic('siaga-alerts', $request->token);

            return response()->json(['message' => 'Subscribed to topic successfully']);
        } catch (\Throwable $e) {
            Log::error('FCM Subscribe Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showAlert()
    {
        $alert = SiagaAlert::where('status', 'active')->first();

        // Cek apakah user sudah hadir
        $alreadyAttended = false;
        if (Auth::guard('personel')->check()) {
            $userId = Auth::guard('personel')->id();
            $attendanceLog = AttendanceLog::where('siaga_alert_id', $alert?->id)
                ->where('personel_id', $userId)
                ->first();

            if ($attendanceLog && $attendanceLog->status === 'hadir') {
                $alreadyAttended = true;
            }
        }

        return view('alert', compact('alert', 'alreadyAttended'));
    }


    public function getAttendanceReport($alertId)
    {
        $alert = SiagaAlert::with(['attendanceLogs' => function($query) {
            $query->whereHas('personel', function($q) {
                $q->where('role_id', 2);
            })->with('personel');
        }])->findOrFail($alertId);

        $report = [
            'alert' => [
                'id' => $alert->id,
                'title' => $alert->title,
                'level' => $alert->level,
                'started_at' => $alert->started_at?->format('Y-m-d H:i:s'),
                'ended_at' => $alert->ended_at?->format('Y-m-d H:i:s'),
                'triggered_by' => $alert->triggered_by,
            ],
            'summary' => [
                'total' => $alert->attendanceLogs->count(),
                'hadir' => $alert->attendanceLogs->where('status', 'hadir')->count(),
                'tidak_hadir' => $alert->attendanceLogs->where('status', 'tidak_hadir')->count(),
                'percentage' => $alert->attendanceLogs->count() > 0
                    ? round(($alert->attendanceLogs->where('status', 'hadir')->count() / $alert->attendanceLogs->count()) * 100, 2)
                    : 0
            ],
            'details' => $alert->attendanceLogs->map(function ($log) {
                return [
                    'personel_name' => $log->personel->name,
                    'nrp' => $log->personel->nrp,
                    'role' => $log->role,
                    'status' => $log->status,
                    'keterangan' => $log->keterangan,
                    'attended_at' => $log->attended_at?->format('Y-m-d H:i:s'),
                    'created_at' => $log->created_at->format('Y-m-d H:i:s')
                ];
            })
        ];

        return response()->json($report);
    }

    public function showAttendanceReport(Request $request)
    {
        $alerts = SiagaAlert::with(['attendanceLogs' => function($query) {
            $query->whereHas('personel', function($q) {
                $q->where('role_id', 2);
            })->with('personel');
        }])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('reports.attendance', compact('alerts'));
    }

    public function exportAttendanceReport($alertId)
    {
        $alert = SiagaAlert::with(['attendanceLogs' => function($query) {
            $query->whereHas('personel', function($q) {
                $q->where('role_id', 2);
            })->with('personel');
        }])->findOrFail($alertId);

        $data = [
            'alert' => $alert,
            'logs' => $alert->attendanceLogs
        ];

        return view('reports.export', $data);
    }

    public function getAlertsForReport()
    {
        $alerts = SiagaAlert::select('id', 'title', 'level', 'created_at', 'status')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'title' => $alert->title,
                    'level' => $alert->level,
                    'date' => $alert->created_at->format('Y-m-d H:i'),
                    'status' => $alert->status,
                    'total_personel' => $alert->attendanceLogs()->count(),
                    'total_hadir' => $alert->attendanceLogs()->where('status', 'hadir')->count(),
                ];
            });

        return response()->json($alerts);
    }
}
