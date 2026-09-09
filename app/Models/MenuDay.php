<?php

namespace App\Models;

use App\Support\WithoutEmDashes;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['day', 'weekday', 'week'])]
class MenuDay extends Model
{
    use WithoutEmDashes;

    public function meals(): HasMany
    {
        return $this->hasMany(MenuMeal::class)->orderBy('sort_order');
    }
}
