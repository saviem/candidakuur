<?php

namespace App\Models;

use App\Support\WithoutEmDashes;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['menu_day_id', 'label', 'text', 'note', 'sort_order'])]
class MenuMeal extends Model
{
    use WithoutEmDashes;

    public function day(): BelongsTo
    {
        return $this->belongsTo(MenuDay::class, 'menu_day_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
