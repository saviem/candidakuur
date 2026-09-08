<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MollieWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_plus_webhook_extends_plus_until(): void
    {
        $user = User::factory()->create(['plus_until' => null]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'mollie_id' => 'tr_test123',
            'type' => 'plus',
            'amount_cents' => 990,
            'currency' => 'EUR',
            'status' => 'open',
            'description' => 'Candidakuur Plus',
            'meta' => ['plus_days' => 30],
        ]);

        Http::fake([
            'https://api.mollie.com/v2/payments/tr_test123' => Http::response([
                'id' => 'tr_test123',
                'status' => 'paid',
                'metadata' => ['payment_id' => $payment->id, 'type' => 'plus'],
            ]),
        ]);

        $this->post(route('mollie.webhook'), ['id' => 'tr_test123'])
            ->assertOk();

        $payment->refresh();
        $user->refresh();

        $this->assertSame('paid', $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertTrue($user->isPlus());
        $this->assertTrue($user->plus_until->greaterThan(now()->addDays(25)));
    }

    public function test_donate_webhook_does_not_unlock_plus(): void
    {
        $user = User::factory()->create(['plus_until' => null]);

        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'mollie_id' => 'tr_donate1',
            'type' => 'donate',
            'amount_cents' => 1000,
            'currency' => 'EUR',
            'status' => 'open',
            'description' => 'Donatie',
            'meta' => ['unlocks_plus' => false],
        ]);

        Http::fake([
            'https://api.mollie.com/v2/payments/tr_donate1' => Http::response([
                'id' => 'tr_donate1',
                'status' => 'paid',
                'metadata' => ['payment_id' => $payment->id, 'type' => 'donate'],
            ]),
        ]);

        $this->post(route('mollie.webhook'), ['id' => 'tr_donate1'])
            ->assertOk();

        $user->refresh();
        $this->assertFalse($user->isPlus());
        $this->assertSame('paid', $payment->fresh()->status);
    }

    public function test_plus_checkout_redirects_to_mollie(): void
    {
        Http::fake([
            'https://api.mollie.com/v2/payments' => Http::response([
                'id' => 'tr_new',
                'status' => 'open',
                '_links' => [
                    'checkout' => ['href' => 'https://www.mollie.com/checkout/test'],
                ],
            ]),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('plus.checkout'))
            ->assertRedirect('https://www.mollie.com/checkout/test');

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'type' => 'plus',
            'mollie_id' => 'tr_new',
        ]);
    }
}
