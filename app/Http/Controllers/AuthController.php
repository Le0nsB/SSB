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
        $credentials = $request->validate($this->loginRules());
        $user = $this->findUserByIdentity($credentials['identity']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->invalidLoginResponse($request);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->is_admin ? 'admin.profile' : 'home');
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

    private function loginRules(): array
    {
        return [
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    private function findUserByIdentity(string $identity): ?User
    {
        return User::query()
            ->where('email', $identity)
            ->orWhere('name', $identity)
            ->first();
    }

    private function invalidLoginResponse(Request $request): RedirectResponse
    {
        return back()
            ->withErrors(['identity' => 'Nepareizs lietotājvārds/e-pasts vai parole.'])
            ->withInput($request->only('identity'));
    }
}
