<?php

namespace App\Models;

use App\Support\WithoutEmDashes;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['guide_id', 'heading', 'body', 'sort_order'])]
class GuideSection extends Model
{
    use WithoutEmDashes;

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }
}
