<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AuthController extends Controller
{
    // ─── Show Login ──────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    // ─── Handle Login ────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'matric_id' => 'required|string',
            'password'  => 'required|string',
        ], [
            'matric_id.required' => 'Sila masukkan Matric Number / ID.',
            'password.required'  => 'Sila masukkan password.',
        ]);

        $credentials = [
            'matric_id' => $request->matric_id,
            'password'  => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect admin to dashboard, user to home
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'));
        }

        return back()
            ->withInput($request->only('matric_id'))
            ->withErrors(['matric_id' => 'Matric Number / ID atau password tidak tepat.']);
    }

    // ─── Show Register ───────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    // ─── Handle Register ─────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'matric_id' => 'required|string|max:50|unique:users,matric_id',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => ['required', 'confirmed', Password::min(6)],
        ], [
            'name.required'      => 'Sila masukkan nama anda.',
            'matric_id.required' => 'Sila masukkan Matric Number / ID.',
            'matric_id.unique'   => 'Matric Number / ID ini sudah berdaftar.',
            'email.required'     => 'Sila masukkan alamat email.',
            'email.unique'       => 'Email ini sudah digunakan.',
            'password.required'  => 'Sila masukkan password.',
            'password.confirmed' => 'Password tidak sepadan. Sila cuba semula.',
            'password.min'       => 'Password mestilah sekurang-kurangnya 6 aksara.',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'matric_id' => $request->matric_id,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    // ─── Logout ──────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berjaya log keluar.');
    }
}