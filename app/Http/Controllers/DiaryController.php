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
        $today = now()->startOfDay();

        if ($request->filled('date')) {
            $selectedDate = Carbon::parse((string) $request->string('date'))->startOfDay();
        } elseif ($request->filled('week')) {
            $weekStart = Carbon::parse((string) $request->string('week'))->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            $selectedDate = $today->betweenIncluded($weekStart, $weekEnd)
                ? $today->copy()
                : $weekStart->copy();
        } else {
            $selectedDate = $today->copy();
        }

        if ($selectedDate->gt($today)) {
            $selectedDate = $today->copy();
        }

        $weekStart = $request->filled('week') && ! $request->filled('date')
            ? Carbon::parse((string) $request->string('week'))->startOfWeek()
            : $selectedDate->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        if ($selectedDate->lt($weekStart) || $selectedDate->gt($weekEnd)) {
            $weekStart = $selectedDate->copy()->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
        }

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

        $selectedKey = $selectedDate->toDateString();
        $entry = $entries->get($selectedKey);

        return view('diary.index', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'entries' => $entries,
            'suggestions' => $suggestions,
            'selectedDate' => $selectedDate,
            'entry' => $entry,
            'symptomOptions' => $this->symptomOptions(),
            'isPlus' => $user->isPlus(),
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

        return $this->redirectToDate($data['entry_date'], 'Dagboek bijgewerkt.');
    }

    public function update(Request $request, DiaryEntry $diary): RedirectResponse
    {
        $this->authorize('update', $diary);

        $data = $this->validated($request);
        $data['entry_date'] = Carbon::parse($data['entry_date'])->toDateString();
        $diary->update($data);

        return $this->redirectToDate($data['entry_date'], 'Dagboek bijgewerkt.');
    }

    public function destroy(DiaryEntry $diary): RedirectResponse
    {
        $this->authorize('delete', $diary);
        $date = $diary->entry_date->toDateString();
        $diary->delete();

        return $this->redirectToDate($date, 'Notitie verwijderd.');
    }

    private function redirectToDate(string $date, string $status): RedirectResponse
    {
        $week = Carbon::parse($date)->startOfWeek()->toDateString();

        return redirect()
            ->route('diary.index', ['date' => $date, 'week' => $week])
            ->with('status', $status);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'entry_date' => ['required', 'date', 'before_or_equal:today'],
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
