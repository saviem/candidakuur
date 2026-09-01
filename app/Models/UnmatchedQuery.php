<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['query_key', 'query', 'count', 'first_seen_at', 'last_seen_at', 'converted_product_id'])]
class UnmatchedQuery extends Model
{
    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function convertedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'converted_product_id');
    }

    public static function record(string $raw): self
    {
        $query = trim(preg_replace('/\s+/', ' ', $raw) ?? '');
        $key = Str::lower($query);

        $row = static::query()->firstOrNew(['query_key' => $key]);
        $row->query = $query;
        $row->count = ($row->exists ? $row->count : 0) + 1;
        $row->first_seen_at ??= now();
        $row->last_seen_at = now();
        $row->save();

        return $row;
    }
}
