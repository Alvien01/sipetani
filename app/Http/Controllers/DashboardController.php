<?php

namespace App\Http\Controllers;

use App\Models\Personel;
use App\Models\SiagaAlert;

class DashboardController extends Controller
{
    public function index()
    {
        // Update user's last active timestamp
        if (auth()->check()) {
            $user = Personel::find(auth()->id());
            if ($user) {
                $user->update(['last_online_at' => now()]);
            }
        }

        $totalPersonel = Personel::count();

        $totalPersonelnoKomandan = Personel::whereHas('role', function ($q) {
            $q->where('name', '!=', 'komandan');
        })->count();
        $dalamSiaga = Personel::whereHas('role', function ($q) {
            $q->where('name', '!=', 'komandan');
        })->where('status', 'Siaga')->count();
        $terkonfirmasi = Personel::whereHas('role', function ($q) {
            $q->where('name', '!=', 'komandan');
        })->where('status', 'Terkonfirmasi')->count();
        $tersedia = Personel::whereHas('role', function ($q) {
            $q->where('name', '!=', 'komandan');
        })->where('status', 'Tersedia')->count();

        $activeAlert = SiagaAlert::where('status', 'active')->first();

        $personels = Personel::latest()->take(10)->get();

        return view('dashboard', compact('totalPersonel', 'dalamSiaga', 'terkonfirmasi', 'tersedia', 'activeAlert', 'personels', 'totalPersonelnoKomandan'));
    }

    public function getStats()
    {
        // Update user's last active timestamp
        if (auth()->check()) {
            $user = Personel::find(auth()->id());
            if ($user) {
                $user->update(['last_online_at' => now()]);
            }
        }

        $totalPersonel = Personel::count();

        $totalPersonelnoKomandan = Personel::whereHas('role', function ($q) {
            $q->where('name', '!=', 'komandan');
        })->count();
        $dalamSiaga = Personel::whereHas('role', function ($q) {
            $q->where('name', '!=', 'komandan');
        })->where('status', 'Siaga')->count();
        $terkonfirmasi = Personel::whereHas('role', function ($q) {
            $q->where('name', '!=', 'komandan');
        })->where('status', 'Terkonfirmasi')->count();
        $tersedia = Personel::whereHas('role', function ($q) {
            $q->where('name', '!=', 'komandan');
        })->where('status', 'Tersedia')->count();

        $activeAlert = SiagaAlert::where('status', 'active')->first();

        $personels = Personel::latest()->take(10)->get();

        return response()->json([
            'totalPersonel' => $totalPersonel,
            'totalPersonelnoKomandan' => $totalPersonelnoKomandan,
            'dalamSiaga' => $dalamSiaga,
            'terkonfirmasi' => $terkonfirmasi,
            'tersedia' => $tersedia,
            'activeAlert' => $activeAlert,
            'personels' => $personels->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'rank' => $p->rank,
                    'position' => $p->position,
                    'status' => $p->status,
                    'nrp' => $p->nrp,
                ];
            }),
        ]);
    }
}
