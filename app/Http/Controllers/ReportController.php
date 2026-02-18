<?php

namespace App\Http\Controllers;

use App\Models\SiagaAlert;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $alerts = SiagaAlert::orderBy('created_at', 'desc')->get();
        
        if ($request->has('alert_id')) {
            // Load alert dengan filter hanya personel dengan role_id = 2 (Personel)
            $selectedAlert = SiagaAlert::with(['attendanceLogs' => function($query) {
                $query->whereHas('personel', function($q) {
                    $q->where('role_id', 2); // Filter hanya role Personel
                })->with('personel');
            }])->find($request->alert_id);
                
            if ($selectedAlert) {
                $attendanceSummary = [
                    'total' => $selectedAlert->attendanceLogs->count(),
                    'hadir' => $selectedAlert->attendanceLogs->where('status', 'hadir')->count(),
                    'tidak_hadir' => $selectedAlert->attendanceLogs->where('status', 'tidak_hadir')->count(),
                    'percentage' => $selectedAlert->attendanceLogs->count() > 0 
                        ? round(($selectedAlert->attendanceLogs->where('status', 'hadir')->count() / $selectedAlert->attendanceLogs->count()) * 100, 2)
                        : 0
                ];
                
                return view('reports.attendance', compact('alerts', 'selectedAlert', 'attendanceSummary'));
            }
        }
        
        return view('reports.attendance', compact('alerts'));
    }

    public function getReport($alertId)
    {
        // Load alert dengan filter hanya personel dengan role_id = 2 (Personel)
        $alert = SiagaAlert::with(['attendanceLogs' => function($query) {
            $query->whereHas('personel', function($q) {
                $q->where('role_id', 2); // Filter hanya role Personel
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

    public function export($alertId)
    {
        // Load alert dengan filter hanya personel dengan role_id = 2 (Personel)
        $alert = SiagaAlert::with(['attendanceLogs' => function($query) {
            $query->whereHas('personel', function($q) {
                $q->where('role_id', 2); // Filter hanya role Personel
            })->with('personel');
        }])->findOrFail($alertId);
        
        $data = [
            'alert' => $alert,
            'logs' => $alert->attendanceLogs
        ];
        
        // Untuk PDF
        // return view('reports.export-pdf', $data);
        
        // Atau untuk Excel
        // return Excel::download(new AttendanceExport($alertId), "laporan-kehadiran-{$alertId}.xlsx");
        
        // Sementara return view dulu
        return view('reports.export', $data);
    }

    public function getAlerts()
    {
        $alerts = SiagaAlert::select('id', 'title', 'level', 'created_at', 'status')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($alert) {
                // Hitung hanya personel dengan role_id = 2 (Personel)
                $totalPersonel = $alert->attendanceLogs()
                    ->whereHas('personel', function($q) {
                        $q->where('role_id', 2);
                    })
                    ->count();
                    
                $totalHadir = $alert->attendanceLogs()
                    ->where('status', 'hadir')
                    ->whereHas('personel', function($q) {
                        $q->where('role_id', 2);
                    })
                    ->count();
                    
                return [
                    'id' => $alert->id,
                    'title' => $alert->title,
                    'level' => $alert->level,
                    'date' => $alert->created_at->format('Y-m-d H:i'),
                    'status' => $alert->status,
                    'total_personel' => $totalPersonel,
                    'total_hadir' => $totalHadir,
                ];
            });
            
        return response()->json($alerts);
    }
}
