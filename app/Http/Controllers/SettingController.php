<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Personel;

class SettingController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        // Get current authenticated personel with role relationship
        $personel = Auth::guard('personel')->user()->load('role');
        
        return view('settings.index', compact('personel'));
    }

    /**
     * Update personel profile
     */
    public function updateProfile(Request $request)
    {
        $personel = Auth::guard('personel')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:personels,email,' . $personel->id,
            'nrp' => 'required|string|max:50|unique:personels,nrp,' . $personel->id,
            'rank' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $personel->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $personel = Auth::guard('personel')->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $personel->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Update password
        $personel->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Password berhasil diperbarui!');
    }
}
