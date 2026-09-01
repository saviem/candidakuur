<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Guide;
use App\Models\Product;
use App\Models\UnmatchedQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnmatchedQueryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:3', 'max:80'],
        ]);

        $query = trim($validated['query']);
        if (preg_match('/https?:|www\.|[<>{}]/i', $query)) {
            return response()->json(['ok' => false], 422);
        }

        $like = '%'.$query.'%';
        $matched = Product::query()
            ->where(function ($builder) use ($like) {
                $builder->where('name', 'like', $like)
                    ->orWhere('aliases', 'like', $like);
            })
            ->exists()
            || Category::query()->where('name', 'like', $like)->orWhere('short', 'like', $like)->exists()
            || Guide::query()->where('title', 'like', $like)->orWhere('summary', 'like', $like)->exists();

        if ($matched) {
            return response()->json(['ok' => true, 'matched' => true]);
        }

        UnmatchedQuery::record($query);

        return response()->json(['ok' => true, 'matched' => false]);
    }
}
