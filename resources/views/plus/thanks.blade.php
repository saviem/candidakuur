@extends('layouts.app')

@section('title', 'Bedankt voor je donatie')

@section('content')
<div class="mx-auto max-w-xl px-4 py-16 sm:px-6 text-center">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Donatie</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight">Dank je wel</h1>
    <p class="mt-4 text-muted">
        Je bijdrage van € {{ number_format($payment->amount_cents / 100, 2, ',', '.') }} helpt Candidakuur draaiende te houden.
        Status: <strong class="text-ink">{{ $payment->status }}</strong>. Dit ontgrendelt geen Plus.
    </p>
    <a href="{{ route('kennisbank') }}" class="mt-8 inline-flex rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper">Terug</a>
</div>
@endsection
