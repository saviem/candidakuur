<?php

namespace App\Models;

use App\Support\WithoutEmDashes;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name', 'short', 'intro', 'access', 'sort_order'])]
class Category extends Model
{
    use WithoutEmDashes;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
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
