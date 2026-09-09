<?php

namespace App\Models;

use App\Support\WithoutEmDashes;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['category_id', 'slug', 'name', 'status', 'why', 'conditions', 'notes', 'aliases', 'access'])]
class Product extends Model
{
    use WithoutEmDashes;

    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'aliases' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function menuMeals(): BelongsToMany
    {
        return $this->belongsToMany(MenuMeal::class);
    }

    public function allergies(): BelongsToMany
    {
        return $this->belongsToMany(Allergy::class);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $inner) use ($like) {
            $inner->where('name', 'like', $like)
                ->orWhere('why', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('aliases', 'like', $like);
        });
    }

    public function related(int $limit = 6)
    {
        return static::query()
            ->where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->limit($limit)
            ->get();
    }
}
