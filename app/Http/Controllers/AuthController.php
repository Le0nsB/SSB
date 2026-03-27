<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->registerRules());

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('team-applications.create');
    }

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

    public function showProfile(): View
    {
        return view('profile.edit');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name')->ignore($user?->id),
            ],
        ]);

        $user->update([
            'name' => $validated['name'],
        ]);

        return back()->with('status', 'Lietotājvārds veiksmīgi nomainīts.');
    }

    private function loginRules(): array
    {
        return [
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    private function registerRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
