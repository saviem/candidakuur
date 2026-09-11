<?php

namespace App\Http\Controllers;

use App\Mail\LoginCodeMail;
use App\Models\LoginCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function createLogin(): View
    {
        return view('auth.login');
    }

    public function sendLoginCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = Str::lower($validated['email']);

        if (! User::query()->where('email', $email)->exists()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Er bestaat nog geen account met dit e-mailadres.']);
        }

        $this->issueCode($email);

        $request->session()->put('auth.email', $email);
        $request->session()->forget('auth.name');

        return redirect()->route('login.verify');
    }

    public function createRegister(): View
    {
        return view('auth.register');
    }

    public function sendRegisterCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $email = Str::lower($validated['email']);

        $this->issueCode($email, $validated['name']);

        $request->session()->put('auth.email', $email);
        $request->session()->put('auth.name', $validated['name']);

        return redirect()->route('login.verify');
    }

    public function createVerify(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('auth.email')) {
            return redirect()->route('login');
        }

        return view('auth.verify', [
            'email' => $request->session()->get('auth.email'),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $email = $request->session()->get('auth.email');

        if (! is_string($email) || $email === '') {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $loginCode = LoginCode::query()
            ->where('email', $email)
            ->latest('id')
            ->first();

        if ($loginCode === null || $loginCode->isExpired() || $loginCode->hasTooManyAttempts()) {
            return back()->withErrors(['code' => 'Deze code is verlopen. Vraag een nieuwe aan.']);
        }

        $loginCode->increment('attempts');

        if (! Hash::check($validated['code'], $loginCode->code)) {
            return back()->withErrors(['code' => 'Deze code klopt niet.']);
        }

        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $name = $loginCode->name ?: $request->session()->get('auth.name');

            if (! is_string($name) || $name === '') {
                return redirect()
                    ->route('register')
                    ->withErrors(['email' => 'Vul eerst je naam en e-mailadres in.']);
            }

            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => null,
                'email_verified_at' => now(),
                'is_admin' => false,
            ]);
        } else {
            $user->forceFill([
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        LoginCode::query()->where('email', $email)->delete();
        $request->session()->forget(['auth.email', 'auth.name']);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('kennisbank'));
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('auth.email');

        if (! is_string($email) || $email === '') {
            return redirect()->route('login');
        }

        $name = $request->session()->get('auth.name');
        $this->issueCode($email, is_string($name) ? $name : null);

        return back()->with('status', 'We hebben een nieuwe code gestuurd.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function issueCode(string $email, ?string $name = null): void
    {
        $plainCode = (string) random_int(100000, 999999);

        LoginCode::query()->where('email', $email)->delete();

        LoginCode::query()->create([
            'email' => $email,
            'code' => Hash::make($plainCode),
            'name' => $name,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($email)->send(new LoginCodeMail($plainCode));
    }
}
