<?php

namespace App\Http\Controllers;

use App\Models\SymptomGuide;
use Illuminate\View\View;

class SymptomGuideController extends Controller
{
    public function index(): View
    {
        return view('symptoms.index', [
            'guides' => SymptomGuide::query()->published()->orderBy('sort_order')->get(),
            'isPlus' => auth()->user()?->isPlus() ?? false,
        ]);
    }

    public function show(SymptomGuide $symptom): View
    {
        abort_unless($symptom->published, 404);

        $isPlus = auth()->user()?->isPlus() ?? false;

        return view('symptoms.show', [
            'guide' => $symptom,
            'isPlus' => $isPlus,
            'others' => SymptomGuide::query()
                ->published()
                ->where('id', '!=', $symptom->id)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
