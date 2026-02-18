<?php

namespace App\Http\Controllers;

use App\Models\Personel;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PersonelController extends Controller
{
    public function index()
    {
        $personels = Personel::with('role')->paginate(10);
        return view('personels.index', compact('personels'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('personels.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:personels,email',
            'phone' => 'required|string|regex:/^08[0-9]{8,11}$/',
            'password' => 'required|string|min:8',
            'nrp' => 'required|string|unique:personels,nrp',
            'rank' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Tersedia,Siaga,Terkonfirmasi',
        ]);

        Personel::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'nrp' => $request->nrp,
            'rank' => $request->rank,
            'position' => $request->position,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        return redirect()->route('personels.index')->with('success', 'Personel berhasil ditambahkan.');
    }

    public function edit(Personel $personel)
    {
        $roles = Role::all();
        return view('personels.edit', compact('personel', 'roles'));
    }

    public function update(Request $request, Personel $personel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:personels,email,' . $personel->id,
            'phone' => 'required|string|regex:/^08[0-9]{8,11}$/',
            'password' => 'nullable|string|min:8',
            'nrp' => 'required|string|unique:personels,nrp,' . $personel->id,
            'rank' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Tersedia,Siaga,Terkonfirmasi',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nrp' => $request->nrp,
            'rank' => $request->rank,
            'position' => $request->position,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $personel->update($data);

        return redirect()->route('personels.index')->with('success', 'Personel berhasil diupdate.');
    }

    public function destroy(Personel $personel)
    {
        $personel->delete();
        return redirect()->route('personels.index')->with('success', 'Personel berhasil dihapus.');
    }
}
