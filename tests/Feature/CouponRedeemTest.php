<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use App\Mail\PlusActivatedMail;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponRedeemTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_redeem_coupon(): void
    {
        $this->post(route('plus.coupon'), ['code' => 'PROBEER7'])
            ->assertRedirect(route('login'));
    }

    public function test_valid_coupon_unlocks_plus(): void
    {
        Mail::fake();

        $user = User::factory()->create(['plus_until' => null]);
        $coupon = Coupon::factory()->create([
            'code' => 'PROBEER7',
            'days' => 7,
        ]);

        $this->actingAs($user)
            ->post(route('plus.coupon'), ['code' => 'probeer7'])
            ->assertRedirect(route('account.edit'))
            ->assertSessionHas('status');

        $user->refresh();
        $coupon->refresh();

        $this->assertTrue($user->isPlus());
        $this->assertTrue($user->plus_until->greaterThan(now()->addDays(5)));
        $this->assertSame(1, $coupon->uses_count);
        Mail::assertSent(PlusActivatedMail::class, function (PlusActivatedMail $mail) use ($user): bool {
            return $mail->hasTo($user->email);
        });
        $this->assertDatabaseHas('coupon_redemptions', [
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'days_granted' => 7,
        ]);
    }

    public function test_coupon_extends_existing_plus(): void
    {
        $user = User::factory()->plus(3)->create();
        $before = $user->plus_until->copy();

        Coupon::factory()->create([
            'code' => 'EXTRA7',
            'days' => 7,
        ]);

        $this->actingAs($user)
            ->post(route('plus.coupon'), ['code' => 'EXTRA7'])
            ->assertRedirect(route('account.edit'));

        $user->refresh();

        $this->assertTrue($user->plus_until->equalTo($before->addDays(7)));
    }

    public function test_same_user_cannot_redeem_coupon_twice(): void
    {
        $user = User::factory()->create();
        Coupon::factory()->create(['code' => 'EENMALIG', 'days' => 7]);

        $this->actingAs($user)
            ->post(route('plus.coupon'), ['code' => 'EENMALIG'])
            ->assertRedirect(route('account.edit'));

        $this->actingAs($user)
            ->from(route('plus.index'))
            ->post(route('plus.coupon'), ['code' => 'EENMALIG'])
            ->assertRedirect(route('plus.index'))
            ->assertSessionHasErrors(['code' => 'Je hebt deze coupon al gebruikt.']);
    }

    public function test_exhausted_coupon_is_rejected(): void
    {
        $user = User::factory()->create();
        Coupon::factory()->exhausted()->create(['code' => 'VOL']);

        $this->actingAs($user)
            ->from(route('plus.index'))
            ->post(route('plus.coupon'), ['code' => 'VOL'])
            ->assertRedirect(route('plus.index'))
            ->assertSessionHasErrors(['code' => 'Deze coupon is al het maximale aantal keren gebruikt.']);

        $this->assertFalse($user->fresh()->isPlus());
    }

    public function test_expired_coupon_is_rejected(): void
    {
        $user = User::factory()->create();
        Coupon::factory()->expired()->create(['code' => 'OUD']);

        $this->actingAs($user)
            ->from(route('plus.index'))
            ->post(route('plus.coupon'), ['code' => 'OUD'])
            ->assertRedirect(route('plus.index'))
            ->assertSessionHasErrors(['code' => 'Deze coupon is verlopen.']);
    }

    public function test_inactive_coupon_is_rejected(): void
    {
        $user = User::factory()->create();
        Coupon::factory()->inactive()->create(['code' => 'UIT']);

        $this->actingAs($user)
            ->from(route('plus.index'))
            ->post(route('plus.coupon'), ['code' => 'UIT'])
            ->assertRedirect(route('plus.index'))
            ->assertSessionHasErrors(['code' => 'Deze coupon is niet meer actief.']);
    }

    public function test_unknown_coupon_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('plus.index'))
            ->post(route('plus.coupon'), ['code' => 'BESTAATNIET'])
            ->assertRedirect(route('plus.index'))
            ->assertSessionHasErrors(['code' => 'Deze couponcode bestaat niet.']);
    }

    public function test_code_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('plus.index'))
            ->post(route('plus.coupon'), [])
            ->assertRedirect(route('plus.index'))
            ->assertSessionHasErrors(['code']);
    }
}
