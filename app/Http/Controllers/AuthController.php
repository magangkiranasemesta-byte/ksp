<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman Login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Menampilkan halaman Register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses Register
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:100',
                'unique:users,username'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed'
            ],

            'role' => [
                'nullable',
                'string',
                'in:SUPERADMIN,ADMIN,ENGINEER,SUPERVISOR,MANAGER'
            ],

            'permissions' => [
                'nullable',
                'array'
            ],
        ]);

        $user = User::create([
            'username'    => $data['username'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => $request->input('role', 'ENGINEER'),
            'permissions' => $request->input('permissions', ['dashboard']),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registrasi berhasil.');
    }

    /**
     * Proses Login
     *
     * Login dapat menggunakan:
     * - Username
     * - Email
     */
    public function login(Request $request)
    {
        // Validasi input dari form login
        $request->validate([
            'login' => [
                'required',
                'string'
            ],

            'password' => [
                'required',
                'string'
            ],
        ]);

        // Ambil input username/email
        $loginInput = trim($request->input('login'));

        // Tentukan apakah input berupa email atau username
        $fieldType = filter_var(
            $loginInput,
            FILTER_VALIDATE_EMAIL
        ) ? 'email' : 'username';

        // Buat credentials untuk Auth::attempt()
        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->input('password'),
        ];

        // Proses autentikasi
        if (!Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {

            return back()
                ->withErrors([
                    'login' => 'Username/Email atau password salah.'
                ])
                ->withInput(
                    $request->only('login')
                );
        }

        // Regenerate session setelah login berhasil
        $request->session()->regenerate();

        // Ambil data user yang sedang login
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN
        |--------------------------------------------------------------------------
        |
        | Superadmin diarahkan ke halaman pemilihan role.
        |
        */

        if (strtoupper($user->role) === 'SUPERADMIN') {

            return redirect()->route('select-role');
        }

        /*
        |--------------------------------------------------------------------------
        | USER BIASA
        |--------------------------------------------------------------------------
        |
        | User selain SUPERADMIN langsung diarahkan ke dashboard.
        |
        */

        return redirect()->intended(
            route('dashboard')
        );
    }

    /**
     * Halaman Pemilihan Role
     */
    public function selectRole()
    {
        // Pastikan user sudah login
        // dan memiliki role SUPERADMIN
        if (
            !Auth::check() ||
            strtoupper(Auth::user()->role) !== 'SUPERADMIN'
        ) {
            return redirect()->route('dashboard');
        }

        return view('auth.select-role');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus session
        $request->session()->invalidate();

        // Generate CSRF token baru
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Dashboard
     */
    public function dashboard(Request $request)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Ambil role aktif
        $activeRole = $request->query(
            'switch_role',
            $user->role
        );

        return view('dashboard.index', [
            'user'       => $user,
            'activeRole' => strtoupper($activeRole)
        ]);
    }
}