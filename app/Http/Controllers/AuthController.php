<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $credentials['identity'])
            ->orWhere('name', $credentials['identity'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['identity' => 'Nepareizs lietotājvārds/e-pasts vai parole.'])
                ->withInput($request->only('identity'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->is_admin) {
            return redirect()->route('admin.profile');
        }

        return redirect()->route('home');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function adminProfile(): View
    {
        if (! auth()->user()?->is_admin) {
            abort(403);
        }

        return view('admin.profile');
    }
}
