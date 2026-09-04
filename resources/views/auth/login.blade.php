@extends('layouts.app')

@section('title', 'Inloggen')

@section('content')
<div class="mx-auto flex max-w-6xl flex-col items-center px-4 py-16 sm:px-6 sm:py-24">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Kennisbank</p>
    <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">Welkom terug</h1>
    <p class="mt-3 max-w-md text-center text-muted">Log in om te zoeken wat je mag eten tijdens de kuur.</p>

    <form method="POST" action="{{ url('/inloggen') }}" class="mt-10 w-full max-w-md rounded-[28px] border border-line bg-card p-6 sm:p-8">
        @csrf
        <label class="block text-sm font-medium text-ink">
            E-mail
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-accent/30 placeholder:text-muted focus:border-accent focus:ring-4">
        </label>
        @error('email')
            <p class="mt-2 text-sm text-deny">{{ $message }}</p>
        @enderror
        <label class="mt-5 block text-sm font-medium text-ink">
            Wachtwoord
            <input type="password" name="password" required autocomplete="current-password" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-accent/30 focus:border-accent focus:ring-4">
        </label>
        <label class="mt-5 flex items-center gap-2 text-sm text-muted">
            <input type="checkbox" name="remember" class="rounded border-line text-accent focus:ring-accent">
            Onthoud mij
        </label>
        <button type="submit" class="mt-8 w-full rounded-full bg-ink px-5 py-3 text-sm font-semibold text-paper transition hover:opacity-90">Inloggen</button>
    </form>
    <p class="mt-6 text-sm text-muted">
        Nog geen account?
        <a href="{{ route('register') }}" class="font-medium text-accent hover:underline">Maak er een aan</a>
    </p>
</div>
@endsection
