@extends('layouts.app')

@section('title', 'Mijn account')

@section('content')
<div class="mx-auto max-w-xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Account</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Mijn account</h1>
    <p class="mt-4 text-muted">Pas je naam en e-mailadres aan. Inloggen blijft via een code in je mail.</p>

    @if (session('status'))
        <p class="mt-6 rounded-[20px] border border-line bg-accent-soft px-4 py-3 text-sm text-accent">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p class="mt-6 rounded-[20px] border border-line bg-deny-soft px-4 py-3 text-sm text-deny">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('account.update') }}" class="mt-10 rounded-[28px] border border-line bg-card p-6 sm:p-8">
        @csrf
        @method('PUT')
        <label class="block text-sm font-medium text-ink">
            Naam
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-accent/30 focus:border-accent focus:ring-4">
        </label>
        @error('name')
            <p class="mt-2 text-sm text-deny">{{ $message }}</p>
        @enderror
        <label class="mt-5 block text-sm font-medium text-ink">
            E-mail
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-accent/30 focus:border-accent focus:ring-4">
        </label>
        @error('email')
            <p class="mt-2 text-sm text-deny">{{ $message }}</p>
        @enderror
        <button type="submit" class="mt-8 w-full rounded-full bg-ink px-5 py-3 text-sm font-semibold text-paper transition hover:opacity-90">Opslaan</button>
    </form>

    @if ($isPlus)
        <section id="plus" class="mt-10 rounded-[28px] border border-line bg-card p-6 sm:p-8">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-accent">Plus</p>
            <h2 class="mt-2 text-lg font-semibold tracking-tight text-ink">Je hebt Plus</h2>
            <p class="mt-2 text-sm text-muted">Actief tot {{ $plusUntil?->timezone(config('app.timezone'))->format('d-m-Y H:i') }}.</p>
            <p class="mt-4 text-sm text-muted">Heb je een couponcode? Die verlengt je Plus-periode.</p>
            <form method="POST" action="{{ route('plus.coupon') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start">
                @csrf
                <div class="flex-1">
                    <label for="coupon-code" class="sr-only">Couponcode</label>
                    <input
                        id="coupon-code"
                        type="text"
                        name="code"
                        value="{{ old('code') }}"
                        autocomplete="off"
                        autocapitalize="characters"
                        placeholder="bijv. PROBEER7"
                        class="w-full rounded-full border border-line bg-paper px-4 py-2.5 text-sm tracking-wide uppercase text-ink placeholder:normal-case placeholder:tracking-normal placeholder:text-muted focus:border-ink/30 focus:outline-none"
                    >
                </div>
                <button type="submit" class="rounded-full border border-line bg-paper px-5 py-2.5 text-sm font-medium transition hover:border-ink/20">
                    Activeer code
                </button>
            </form>
        </section>
    @else
        <section class="mt-10 rounded-[28px] border border-line bg-accent-soft p-6 sm:p-8">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-accent">Plus</p>
            <h2 class="mt-2 text-lg font-semibold tracking-tight text-ink">Nog geen Plus</h2>
            <p class="mt-2 text-sm text-muted">Onbeperkte assistent, dagboek en volledige Wat nu?-kaarten.</p>
            <a href="{{ route('plus.index') }}" class="mt-5 inline-flex rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:opacity-90">Bekijk Plus</a>
        </section>
    @endif

    <section class="mt-10 rounded-[28px] border border-deny/30 bg-card p-6 sm:p-8">
        <h2 class="text-lg font-semibold tracking-tight text-ink">Account verwijderen</h2>
        <p class="mt-2 text-sm leading-relaxed text-muted">
            Dit wist je account, dagboek en opgeslagen menu's permanent. Betalingen blijven voor de administratie bewaard zonder koppeling aan jou.
        </p>
        <form method="POST" action="{{ route('account.destroy') }}" class="mt-6" onsubmit="return confirm('Weet je zeker dat je je account wilt verwijderen? Dit kan niet ongedaan worden gemaakt.');">
            @csrf
            @method('DELETE')
            <label class="block text-sm font-medium text-ink">
                Bevestig met je e-mailadres
                <input type="email" name="confirm_email" required autocomplete="email" placeholder="{{ $user->email }}" class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-base text-ink outline-none ring-deny/30 focus:border-deny focus:ring-4">
            </label>
            @error('confirm_email')
                <p class="mt-2 text-sm text-deny">{{ $message }}</p>
            @enderror
            <button type="submit" class="mt-6 w-full rounded-full border border-deny bg-paper px-5 py-3 text-sm font-semibold text-deny transition hover:bg-deny hover:text-paper">Account definitief verwijderen</button>
        </form>
    </section>
</div>
@endsection
