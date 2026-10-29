<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:Admin,Petugas,Pengguna',
            'status'   => 'required|in:Aktif,Nonaktif'
        ]);

        $data['password'] = $request->password;

        $user = User::create($data);

        LogAktivitas::record('create', 'User', "User {$user->nama} ditambahkan sebagai {$user->role}");

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        return view('admin.user.form', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|in:Admin,Petugas,Pengguna',
            'status'   => 'required|in:Aktif,Nonaktif'
        ]);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        } else {
            unset($data['password']);
        }

        $user->update($data);

        LogAktivitas::record('update', 'User', "Data user {$user->nama} diperbarui");

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Anda tidak bisa menghapus akun sendiri');
        }

        $user->delete();

        LogAktivitas::record('delete', 'User', "User {$user->nama} dihapus");

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil dihapus');
    }
}
