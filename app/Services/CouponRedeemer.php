<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CouponRedeemer
{
    public function redeem(User $user, string $code): Coupon
    {
        $normalized = Str::upper(trim($code));

        return DB::transaction(function () use ($user, $normalized) {
            $coupon = Coupon::query()
                ->where('code', $normalized)
                ->lockForUpdate()
                ->first();

            if (! $coupon) {
                throw ValidationException::withMessages([
                    'code' => 'Deze couponcode bestaat niet.',
                ]);
            }

            if (! $coupon->is_active) {
                throw ValidationException::withMessages([
                    'code' => 'Deze coupon is niet meer actief.',
                ]);
            }

            if ($coupon->isExpired()) {
                throw ValidationException::withMessages([
                    'code' => 'Deze coupon is verlopen.',
                ]);
            }

            if ($coupon->hasReachedMaxUses()) {
                throw ValidationException::withMessages([
                    'code' => 'Deze coupon is al het maximale aantal keren gebruikt.',
                ]);
            }

            $alreadyRedeemed = CouponRedemption::query()
                ->where('coupon_id', $coupon->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($alreadyRedeemed) {
                throw ValidationException::withMessages([
                    'code' => 'Je hebt deze coupon al gebruikt.',
                ]);
            }

            CouponRedemption::query()->create([
                'coupon_id' => $coupon->id,
                'user_id' => $user->id,
                'days_granted' => $coupon->days,
            ]);

            $coupon->increment('uses_count');

            $base = $user->plus_until && $user->plus_until->isFuture()
                ? $user->plus_until
                : now();

            $user->forceFill([
                'plus_until' => $base->copy()->addDays($coupon->days),
            ])->save();

            return $coupon->fresh();
        });
    }
}
