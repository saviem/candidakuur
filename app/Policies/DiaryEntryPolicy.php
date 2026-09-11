<?php

namespace App\Policies;

use App\Models\DiaryEntry;
use App\Models\User;

class DiaryEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DiaryEntry $diaryEntry): bool
    {
        return $user->isPlus() && $user->id === $diaryEntry->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isPlus();
    }

    public function update(User $user, DiaryEntry $diaryEntry): bool
    {
        return $user->isPlus() && $user->id === $diaryEntry->user_id;
    }

    public function delete(User $user, DiaryEntry $diaryEntry): bool
    {
        return $user->isPlus() && $user->id === $diaryEntry->user_id;
    }
}
