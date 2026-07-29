<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Ensure admin user exists
        if (!User::where('email', 'admin@ahsapevim.com')->exists()) {
            User::create([
                'name' => 'AhşapEvim Admin',
                'email' => 'admin@ahsapevim.com',
                'password' => bcrypt('admin1234'),
            ]);
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.products.index'))->with('success', 'Başarıyla giriş yapıldı.');
        }

        return back()->withErrors([
            'email' => 'Girilen bilgiler kayıtlarımızla eşleşmedi.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Başarıyla çıkış yapıldı.');
    }
}
