@extends('layouts.app')

@section('title', 'Account aanmaken')

@section('content')
<div class="mx-auto flex max-w-6xl flex-col items-center px-4 py-16 sm:px-6 sm:py-24">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Kennisbank</p>
    <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">Account aanmaken</h1>
    <p class="mt-3 max-w-md text-center text-muted">Daarna kun je producten opzoeken, het menu volgen en de gids lezen.</p>

    <form method="POST" action="{{ url('/account') }}" class="mt-10 w-full max-w-md rounded-[28px] border border-line bg-card p-6 sm:p-8">
        @csrf
        <label class="block text-sm font-medium text-ink">
            Naam
            <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-accent/30 focus:border-accent focus:ring-4">
        </label>
        @error('name')
            <p class="mt-2 text-sm text-deny">{{ $message }}</p>
        @enderror
        <label class="mt-5 block text-sm font-medium text-ink">
            E-mail
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-accent/30 focus:border-accent focus:ring-4">
        </label>
        @error('email')
            <p class="mt-2 text-sm text-deny">{{ $message }}</p>
        @enderror
        <label class="mt-5 block text-sm font-medium text-ink">
            Wachtwoord
            <input type="password" name="password" required autocomplete="new-password" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-accent/30 focus:border-accent focus:ring-4">
        </label>
        @error('password')
            <p class="mt-2 text-sm text-deny">{{ $message }}</p>
        @enderror
        <label class="mt-5 block text-sm font-medium text-ink">
            Wachtwoord bevestigen
            <input type="password" name="password_confirmation" required autocomplete="new-password" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-accent/30 focus:border-accent focus:ring-4">
        </label>
        <button type="submit" class="mt-8 w-full rounded-full bg-ink px-5 py-3 text-sm font-semibold text-paper transition hover:opacity-90">Account aanmaken</button>
    </form>
    <p class="mt-6 text-sm text-muted">
        Al een account?
        <a href="{{ route('login') }}" class="font-medium text-accent hover:underline">Inloggen</a>
    </p>
</div>
@endsection
