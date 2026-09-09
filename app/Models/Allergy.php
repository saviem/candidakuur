<?php

namespace App\Models;

use App\Support\WithoutEmDashes;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['slug', 'name', 'short', 'intro', 'is_main', 'sort_order'])]
class Allergy extends Model
{
    use WithoutEmDashes;

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function counts(): array
    {
        $products = $this->products;
        $allowed = $products->whereIn('status', [
            ProductStatus::Toegestaan,
            ProductStatus::Beperkt,
        ])->count();

        return [
            'total' => $products->count(),
            'toegestaan' => $allowed,
            'niet' => $products->where('status', ProductStatus::NietToegestaan)->count(),
        ];
    }
}
