<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan form login dengan 3 input (Username, Password, Tahun Periode).
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Proses Autentikasi Khusus dengan Username, Password, dan Tahun Anggaran.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'year' => 'required|numeric|digits:4',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'year.required' => 'Tahun Anggaran / Periode Laporan wajib dipilih.',
            'year.digits' => 'Format tahun harus 4 digit angka (misal: 2026).',
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Simpan parameter Tahun Anggaran ke dalam Session
            session(['active_year' => (int) $credentials['year']]);

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, " . Auth::user()->name . " (Periode Laporan Tahun {$credentials['year']}).");
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan tidak cocok.',
        ])->onlyInput('username', 'year');
    }

    /**
     * Keluar dari sistem (Logout).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
