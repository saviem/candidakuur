@extends('layouts.app')

@section('title', 'Plus betaling')

@section('content')
<div class="mx-auto max-w-xl px-4 py-16 sm:px-6 text-center">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Candidakuur Plus</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight">Bedankt</h1>
    <p class="mt-4 text-muted">
        Status: <strong class="text-ink">{{ $payment->status }}</strong>.
        @if ($isPlus)
            Plus is actief.
        @else
            De bevestiging via Mollie kan even duren, vernieuw deze pagina zo nodig.
        @endif
    </p>
    <a href="{{ route('kennisbank') }}" class="mt-8 inline-flex rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper">Naar de kennisbank</a>
</div>
@endsection
