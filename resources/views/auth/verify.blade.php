@extends('layouts.app')

@section('title', 'Inlogcode')

@section('content')
<div class="mx-auto flex max-w-6xl flex-col items-center px-4 py-16 sm:px-6 sm:py-24">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Kennisbank</p>
    <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">Check je mail</h1>
    <p class="mt-3 max-w-md text-center text-muted">
        We hebben een code van 6 cijfers gestuurd naar
        <span class="font-medium text-ink">{{ $email }}</span>.
    </p>

    <form method="POST" action="{{ route('login.verify.submit') }}" id="login-code-form" class="mt-10 w-full max-w-md rounded-[28px] border border-line bg-card p-6 sm:p-8">
        @csrf
        @if (session('status'))
            <p class="mb-5 rounded-2xl border border-accent/20 bg-accent-soft/60 px-4 py-3 text-sm text-ink">{{ session('status') }}</p>
        @endif

        <p class="text-sm font-medium text-ink">Code</p>
        <input type="hidden" name="code" id="login-code" value="{{ old('code') }}">

        <div class="mt-2 flex justify-between gap-2 sm:gap-3" data-code-inputs>
            @foreach (range(0, 5) as $index)
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="1"
                    autocomplete="{{ $index === 0 ? 'one-time-code' : 'off' }}"
                    aria-label="Cijfer {{ $index + 1 }} van 6"
                    @if ($index === 0) autofocus @endif
                    class="code-digit h-14 w-full max-w-14 rounded-2xl border border-line bg-paper text-center text-2xl font-semibold text-ink outline-none ring-accent/30 focus:border-accent focus:ring-4"
                >
            @endforeach
        </div>

        @error('code')
            <p class="mt-2 text-sm text-deny">{{ $message }}</p>
        @enderror
        <button type="submit" class="mt-8 w-full rounded-full bg-ink px-5 py-3 text-sm font-semibold text-paper transition hover:opacity-90">Bevestigen</button>
    </form>

    <form method="POST" action="{{ route('login.resend') }}" class="mt-6">
        @csrf
        <button type="submit" class="text-sm font-medium text-accent hover:underline">Stuur opnieuw</button>
    </form>
    <p class="mt-4 text-sm text-muted">
        Verkeerd e-mailadres?
        <a href="{{ route('login') }}" class="font-medium text-accent hover:underline">Opnieuw beginnen</a>
    </p>
</div>

<script>
(() => {
    const form = document.getElementById('login-code-form');
    const hidden = document.getElementById('login-code');
    const inputs = [...form.querySelectorAll('.code-digit')];

    const sync = () => {
        hidden.value = inputs.map((input) => input.value).join('');
    };

    const fill = (digits) => {
        digits.slice(0, inputs.length).forEach((digit, index) => {
            inputs[index].value = digit;
        });
        sync();
        const next = inputs[Math.min(digits.length, inputs.length - 1)];
        next?.focus();
        if (digits.length >= inputs.length) {
            form.requestSubmit();
        }
    };

    const existing = (hidden.value || '').replace(/\D/g, '').slice(0, 6);
    if (existing) {
        fill(existing.split(''));
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', (event) => {
            const value = event.target.value.replace(/\D/g, '');

            if (value.length > 1) {
                fill(value.split(''));
                return;
            }

            event.target.value = value.slice(-1);
            sync();

            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            if (inputs.every((field) => field.value !== '')) {
                form.requestSubmit();
            }
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
                inputs[index - 1].value = '';
                sync();
            }

            if (event.key === 'ArrowLeft' && index > 0) {
                event.preventDefault();
                inputs[index - 1].focus();
            }

            if (event.key === 'ArrowRight' && index < inputs.length - 1) {
                event.preventDefault();
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('paste', (event) => {
            event.preventDefault();
            const pasted = (event.clipboardData?.getData('text') || '').replace(/\D/g, '');
            if (pasted) {
                fill(pasted.split(''));
            }
        });

        input.addEventListener('focus', () => {
            input.select();
        });
    });

    form.addEventListener('submit', sync);
})();
</script>
@endsection
