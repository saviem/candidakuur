<?php

namespace App\Models;

use App\Support\WithoutEmDashes;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'title', 'summary', 'access', 'sort_order'])]
class Guide extends Model
{
    use WithoutEmDashes;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sections(): HasMany
    {
        return $this->hasMany(GuideSection::class)->orderBy('sort_order');
    }
}
