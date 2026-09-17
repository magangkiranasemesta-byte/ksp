<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        
        // List daftar fitur/modul yang ada di aplikasi
        $availablePermissions = [
            'dashboard'   => 'Dashboard Overview',
            'tickets'     => 'Kelola Ticket',
            'maintenance' => 'Kelola Maintenance Request',
            'history'     => 'Lihat Maintenance History',
            'equipment'   => 'Kelola Equipment',
            'spareparts'  => 'Kelola Sparepart',
            'users'       => 'User Management & Hak Akses',
        ];

        return view('users.index', compact('users', 'availablePermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'    => 'required|string|unique:users',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:6',
            'role'        => 'required|in:SUPERADMIN,ADMIN,ENGINEER,SUPERVISOR,MANAGER',
            'permissions' => 'nullable|array',
        ]);

        User::create([
            'name'        => $request->username,
            'username'    => $request->username,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan!');
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'role'        => 'required|in:SUPERADMIN,ADMIN,ENGINEER,SUPERVISOR,MANAGER',
            'permissions' => 'nullable|array',
        ]);

        $user->update([
            'role'        => $request->role,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->back()->with('success', 'Hak akses user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Kamu tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}