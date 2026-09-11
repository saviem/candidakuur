<?php

namespace Tests\Feature;

use App\Mail\LoginCodeMail;
use App\Models\LoginCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LoginCodeAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_an_existing_account(): void
    {
        Mail::fake();

        $this->post('/inloggen', [
            '_token' => csrf_token(),
            'email' => 'onbekend@example.com',
        ])->assertRedirect()
            ->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    public function test_wrong_code_is_rejected(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);

        $this->post('/inloggen', [
            '_token' => csrf_token(),
            'email' => $user->email,
        ])->assertRedirect(route('login.verify'));

        LoginCode::query()->where('email', $user->email)->update([
            'code' => Hash::make('111111'),
        ]);

        $this->from(route('login.verify'))
            ->post('/inloggen/code', [
                '_token' => csrf_token(),
                'code' => '999999',
            ])
            ->assertRedirect(route('login.verify'))
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_expired_code_is_rejected(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'verlopen@example.com']);

        $this->post('/inloggen', [
            '_token' => csrf_token(),
            'email' => $user->email,
        ]);

        LoginCode::query()->where('email', $user->email)->update([
            'code' => Hash::make('222222'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->from(route('login.verify'))
            ->post('/inloggen/code', [
                '_token' => csrf_token(),
                'code' => '222222',
            ])
            ->assertRedirect(route('login.verify'))
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_users_can_resend_a_code(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'opnieuw@example.com']);

        $this->post('/inloggen', [
            '_token' => csrf_token(),
            'email' => $user->email,
        ]);

        Mail::assertSent(LoginCodeMail::class, 1);

        $this->from(route('login.verify'))
            ->post('/inloggen/code/opnieuw', [
                '_token' => csrf_token(),
            ])
            ->assertRedirect(route('login.verify'))
            ->assertSessionHas('status');

        Mail::assertSent(LoginCodeMail::class, 2);
        $this->assertSame(1, LoginCode::query()->where('email', $user->email)->count());
    }

    public function test_verify_page_requires_pending_email(): void
    {
        $this->get('/inloggen/code')->assertRedirect(route('login'));
    }
}
