<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MollieClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use RuntimeException;

class PlusController extends Controller
{
    public function index(): View
    {
        return view('plus.index', [
            'configured' => app(MollieClient::class)->configured(),
            'isPlus' => auth()->user()?->isPlus() ?? false,
            'plusUntil' => auth()->user()?->plus_until,
            'plusPrice' => config('services.mollie.plus_amount'),
            'donateAmounts' => config('services.mollie.donate_amounts'),
        ]);
    }

    public function checkoutPlus(Request $request, MollieClient $mollie): RedirectResponse
    {
        if (! $mollie->configured()) {
            return back()->withErrors(['plus' => 'Mollie is nog niet geconfigureerd. Voeg MOLLIE_KEY toe.']);
        }

        $amount = (string) config('services.mollie.plus_amount');
        $cents = (int) round(((float) $amount) * 100);
        $days = (int) config('services.mollie.plus_days', 30);

        $payment = Payment::query()->create([
            'user_id' => $request->user()->id,
            'type' => 'plus',
            'amount_cents' => $cents,
            'currency' => 'EUR',
            'status' => 'open',
            'description' => 'Candidakuur Plus ('.$days.' dagen)',
            'meta' => ['plus_days' => $days],
        ]);

        try {
            $molliePayment = $mollie->createPayment([
                'amount' => ['currency' => 'EUR', 'value' => number_format($cents / 100, 2, '.', '')],
                'description' => $payment->description,
                'redirectUrl' => route('plus.return', $payment),
                'webhookUrl' => route('mollie.webhook'),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'type' => 'plus',
                    'user_id' => $request->user()->id,
                ],
            ]);
        } catch (RuntimeException $exception) {
            $payment->update(['status' => 'failed']);

            return back()->withErrors(['plus' => $exception->getMessage()]);
        }

        $payment->update([
            'mollie_id' => $molliePayment['id'] ?? null,
            'status' => $molliePayment['status'] ?? 'open',
        ]);

        $checkout = $molliePayment['_links']['checkout']['href'] ?? null;
        if (! is_string($checkout) || $checkout === '') {
            return back()->withErrors(['plus' => 'Geen Mollie checkout-URL ontvangen.']);
        }

        return redirect()->away($checkout);
    }

    public function checkoutDonate(Request $request, MollieClient $mollie): RedirectResponse
    {
        if (! $mollie->configured()) {
            return back()->withErrors(['donate' => 'Mollie is nog niet geconfigureerd. Voeg MOLLIE_KEY toe.']);
        }

        $allowed = collect(config('services.mollie.donate_amounts', [5, 10, 25]))
            ->map(fn ($v) => (int) $v)
            ->all();

        $euros = (int) $request->validate([
            'amount' => ['required', 'integer', 'in:'.implode(',', $allowed)],
        ])['amount'];

        $cents = $euros * 100;

        $payment = Payment::query()->create([
            'user_id' => $request->user()?->id,
            'type' => 'donate',
            'amount_cents' => $cents,
            'currency' => 'EUR',
            'status' => 'open',
            'description' => 'Donatie Candidakuur €'.$euros,
            'meta' => ['unlocks_plus' => false],
        ]);

        try {
            $molliePayment = $mollie->createPayment([
                'amount' => ['currency' => 'EUR', 'value' => number_format($cents / 100, 2, '.', '')],
                'description' => $payment->description,
                'redirectUrl' => route('donate.thanks', $payment),
                'webhookUrl' => route('mollie.webhook'),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'type' => 'donate',
                    'user_id' => $request->user()?->id,
                ],
            ]);
        } catch (RuntimeException $exception) {
            $payment->update(['status' => 'failed']);

            return back()->withErrors(['donate' => $exception->getMessage()]);
        }

        $payment->update([
            'mollie_id' => $molliePayment['id'] ?? null,
            'status' => $molliePayment['status'] ?? 'open',
        ]);

        $checkout = $molliePayment['_links']['checkout']['href'] ?? null;
        if (! is_string($checkout) || $checkout === '') {
            return back()->withErrors(['donate' => 'Geen Mollie checkout-URL ontvangen.']);
        }

        return redirect()->away($checkout);
    }

    public function returnPlus(Payment $payment): View|RedirectResponse
    {
        abort_unless($payment->type === 'plus', 404);
        abort_unless($payment->user_id === auth()->id(), 403);

        return view('plus.return', [
            'payment' => $payment->fresh(),
            'isPlus' => auth()->user()?->isPlus() ?? false,
        ]);
    }

    public function thanksDonate(Payment $payment): View
    {
        abort_unless($payment->type === 'donate', 404);

        return view('plus.thanks', [
            'payment' => $payment->fresh(),
        ]);
    }

    public function webhook(Request $request, MollieClient $mollie): Response
    {
        $id = (string) $request->input('id');
        if ($id === '') {
            return response('Missing id', 400);
        }

        if (! $mollie->configured()) {
            return response('Mollie not configured', 503);
        }

        $remote = $mollie->getPayment($id);
        $payment = Payment::query()->where('mollie_id', $id)->first();

        if (! $payment) {
            $metaId = data_get($remote, 'metadata.payment_id');
            if ($metaId) {
                $payment = Payment::query()->find($metaId);
            }
        }

        if (! $payment) {
            return response('Unknown payment', 404);
        }

        $status = (string) ($remote['status'] ?? $payment->status);
        $payment->status = $status;

        if ($status === 'paid' && ! $payment->paid_at) {
            $payment->paid_at = now();
            $payment->save();

            if ($payment->type === 'plus' && $payment->user) {
                $days = (int) data_get($payment->meta, 'plus_days', config('services.mollie.plus_days', 30));
                $base = $payment->user->plus_until && $payment->user->plus_until->isFuture()
                    ? $payment->user->plus_until
                    : now();
                $payment->user->forceFill([
                    'plus_until' => $base->copy()->addDays($days),
                ])->save();
            }
        } else {
            $payment->save();
        }

        return response('OK', 200);
    }
}
