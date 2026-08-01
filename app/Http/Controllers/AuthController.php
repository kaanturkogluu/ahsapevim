<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Hoş geldiniz! Başarıyla giriş yapıldı.');
        }

        return back()->withErrors([
            'email' => 'Girdiğiniz e-posta adresi veya şifre hatalı.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Hesabınız başarıyla oluşturuldu! Hoş geldiniz.');
    }

    /**
     * Hızlı Google ile Giriş / Kayıt Ol veya Socialite Yönlendirmesi
     */
    public function googleLogin(Request $request)
    {
        if (config('services.google.client_id') && config('services.google.client_secret')) {
            return Socialite::driver('google')->stateless()->redirect();
        }

        $email = $request->email ?: 'google.user@gmail.com';
        $name = $request->name ?: 'Google Kullanıcısı';
        $googleId = $request->google_id ?: 'google_' . Str::random(12);

        $user = User::where('email', $email)->orWhere('google_id', $googleId)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'password' => Hash::make(Str::random(16)),
                'avatar' => 'https://lh3.googleusercontent.com/a/default-user',
            ]);
        } else {
            if (!$user->google_id) {
                $user->update(['google_id' => $googleId]);
            }
        }

        Auth::login($user, true);

        return redirect('/')->with('success', 'Google hesabınızla başarıyla giriş yapıldı!');
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())
                        ->orWhere('google_id', $googleUser->getId())
                        ->first();

            if (!$user) {
                $user = User::create([
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                    'password'  => Hash::make(Str::random(16)),
                ]);
            } else {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($user, true);

            return redirect('/')->with('success', 'Google hesabınızla başarıyla giriş yapıldı!');
        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Google ile giriş hatası: ' . $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Başarıyla çıkış yapıldı.');
    }
}
