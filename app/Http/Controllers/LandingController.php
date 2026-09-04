<?php

namespace App\Http\Controllers;

use App\Models\Allergy;
use App\Models\Category;
use App\Models\Guide;
use App\Models\Product;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        return view('landing', [
            'productCount' => Product::query()->count(),
            'categoryCount' => Category::query()->count(),
            'guideCount' => Guide::query()->count(),
            'allergyCount' => Allergy::query()->count(),
        ]);
    }
}
