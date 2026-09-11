<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => Str::upper(Str::random(8)),
            'label' => fake()->optional()->words(2, true),
            'days' => 7,
            'max_uses' => null,
            'uses_count' => 0,
            'expires_at' => null,
            'is_active' => true,
        ];
    }

    public function exhausted(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_uses' => 1,
            'uses_count' => 1,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
