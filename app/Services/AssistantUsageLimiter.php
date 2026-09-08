<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AssistantUsageLimiter
{
    public const FREE_DAILY_LIMIT = 3;

    public function remaining(User $user): ?int
    {
        if ($user->isPlus()) {
            return null;
        }

        return max(0, self::FREE_DAILY_LIMIT - $this->usedToday($user));
    }

    public function canGenerate(User $user): bool
    {
        return $user->isPlus() || $this->usedToday($user) < self::FREE_DAILY_LIMIT;
    }

    public function hit(User $user): void
    {
        if ($user->isPlus()) {
            return;
        }

        $key = $this->key($user);
        if (! Cache::has($key)) {
            Cache::put($key, 1, now()->endOfDay());

            return;
        }

        Cache::increment($key);
    }

    public function usedToday(User $user): int
    {
        return (int) Cache::get($this->key($user), 0);
    }

    private function key(User $user): string
    {
        return 'assistant:generations:'.$user->id.':'.now()->toDateString();
    }
}
