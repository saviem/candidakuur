<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginCode extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'code',
        'name',
        'attempts',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function hasTooManyAttempts(): bool
    {
        return $this->attempts >= 5;
    }
}
