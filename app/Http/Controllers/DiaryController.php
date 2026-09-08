<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use App\Models\SymptomGuide;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiaryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', DiaryEntry::class);

        $user = $request->user();
        $weekStart = Carbon::parse((string) $request->string('week', now()->startOfWeek()->toDateString()))->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $entries = DiaryEntry::query()
            ->where('user_id', $user->id)
            ->whereBetween('entry_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('entry_date')
            ->get()
            ->keyBy(fn (DiaryEntry $entry) => $entry->entry_date->toDateString());

        $recentTags = DiaryEntry::query()
            ->where('user_id', $user->id)
            ->where('entry_date', '>=', now()->subDays(14)->toDateString())
            ->pluck('symptom_tags')
            ->filter()
            ->flatten()
            ->countBy()
            ->filter(fn (int $count) => $count >= 2)
            ->keys()
            ->all();

        $suggestions = collect();
        if ($recentTags !== []) {
            $suggestions = SymptomGuide::query()
                ->published()
                ->orderBy('sort_order')
                ->get()
                ->filter(function (SymptomGuide $guide) use ($recentTags) {
                    $tags = collect($guide->tags ?? [])->map(fn ($t) => mb_strtolower((string) $t));

                    return collect($recentTags)->contains(fn ($tag) => $tags->contains(mb_strtolower((string) $tag)));
                })
                ->values();
        }

        return view('diary.index', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'entries' => $entries,
            'suggestions' => $suggestions,
            'today' => $entries->get(now()->toDateString()),
            'symptomOptions' => $this->symptomOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', DiaryEntry::class);

        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        $data['entry_date'] = Carbon::parse($data['entry_date'])->toDateString();

        $entry = DiaryEntry::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('entry_date', $data['entry_date'])
            ->first();

        if ($entry) {
            $entry->update($data);
        } else {
            DiaryEntry::query()->create($data);
        }

        return redirect()
            ->route('diary.index')
            ->with('status', 'Dagboek bijgewerkt.');
    }

    public function update(Request $request, DiaryEntry $diary): RedirectResponse
    {
        $this->authorize('update', $diary);

        $diary->update($this->validated($request));

        return redirect()
            ->route('diary.index')
            ->with('status', 'Dagboek bijgewerkt.');
    }

    public function destroy(DiaryEntry $diary): RedirectResponse
    {
        $this->authorize('delete', $diary);
        $diary->delete();

        return redirect()
            ->route('diary.index')
            ->with('status', 'Notitie verwijderd.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'mood' => ['required', 'integer', 'min:1', 'max:5'],
            'energy' => ['required', 'integer', 'min:1', 'max:5'],
            'symptom_tags' => ['nullable', 'array'],
            'symptom_tags.*' => ['string', 'max:40'],
            'note' => ['nullable', 'string', 'max:2000'],
            'meals' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['symptom_tags'] = array_values(array_unique($validated['symptom_tags'] ?? []));

        return $validated;
    }

    /**
     * @return list<string>
     */
    private function symptomOptions(): array
    {
        return [
            'spierpijn',
            'grieperig',
            'hoofdpijn',
            'moeheid',
            'honger',
            'cravings',
            'verstopping',
            'losse ontlasting',
            'huid',
            'jeuk',
            'erger',
        ];
    }
}
