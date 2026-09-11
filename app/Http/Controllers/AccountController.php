<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('account.edit', [
            'user' => $user,
            'isPlus' => $user->isPlus(),
            'plusUntil' => $user->plus_until,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $email = strtolower($validated['email']);

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $email,
        ])->save();

        return redirect()
            ->route('account.edit')
            ->with('status', 'Je gegevens zijn opgeslagen.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'confirm_email' => ['required', 'email'],
        ]);

        if (strtolower($validated['confirm_email']) !== strtolower($user->email)) {
            return back()->withErrors([
                'confirm_email' => 'Dit e-mailadres komt niet overeen met je account.',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()
            ->route('home')
            ->with('status', 'Je account is verwijderd.');
    }
}
