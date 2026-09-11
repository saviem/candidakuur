<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class FridgeMenuGenerator
{
    /**
     * @return array{title: string, intro: string, meals: list<array{label: string, text: string, note: ?string}>, tips: list<string>, warning: ?string, products: list<array{name: string, product: ?Product}>}
     */
    public function generate(string $first, string $second, ?string $firstSlug = null, ?string $secondSlug = null): array
    {
        $resolved = [
            ['name' => $first, 'product' => $this->resolve($first, $firstSlug)],
            ['name' => $second, 'product' => $this->resolve($second, $secondSlug)],
        ];

        $payload = $this->complete($resolved, $first, $second);

        return [
            'title' => $payload['title'],
            'intro' => $payload['intro'],
            'meals' => $payload['meals'],
            'tips' => $payload['tips'],
            'warning' => $payload['warning'],
            'products' => $resolved,
        ];
    }

    public function resolve(string $name, ?string $slug): ?Product
    {
        if (filled($slug)) {
            $match = Product::query()->with('category')->where('slug', $slug)->first();

            if ($match) {
                return $match;
            }
        }

        $term = trim($name);

        return Product::query()
            ->with('category')
            ->where(function ($query) use ($term) {
                $query->where('name', $term)
                    ->orWhere('slug', Str::slug($term))
                    ->orWhere('aliases', 'like', '%'.$term.'%');
            })
            ->orderByRaw('CASE WHEN name = ? THEN 0 ELSE 1 END', [$term])
            ->first();
    }

    /**
     * @param  list<array{name: string, product: ?Product}>  $resolved
     * @return array{title: string, intro: string, meals: list<array{label: string, text: string, note: ?string}>, tips: list<string>, warning: ?string}
     */
    private function complete(array $resolved, string $first, string $second): array
    {
        $key = config('services.openrouter.key');
        $baseUrl = rtrim((string) config('services.openrouter.url'), '/');
        $model = (string) config('services.openrouter.model');

        if (! is_string($key) || $key === '') {
            throw new RuntimeException('De AI-assistent is nog niet geconfigureerd.');
        }

        $response = Http::baseUrl($baseUrl)
            ->withToken($key)
            ->acceptJson()
            ->timeout(45)
            ->withHeaders([
                'HTTP-Referer' => (string) config('app.url'),
                'X-Title' => (string) config('app.name'),
            ])
            ->post('/chat/completions', [
                'model' => $model,
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user', 'content' => $this->userPrompt($resolved, $first, $second)],
                ],
            ]);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException('De assistent is even niet bereikbaar. Probeer het zo opnieuw.', previous: $exception);
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($content) || $content === '') {
            throw new RuntimeException('De assistent gaf geen bruikbaar menu terug.');
        }

        return $this->parse($content);
    }

    /**
     * @param  list<array{name: string, product: ?Product}>  $resolved
     */
    private function userPrompt(array $resolved, string $first, string $second): string
    {
        $lines = [
            "De gebruiker heeft deze twee producten in de koelkast: {$first} en {$second}.",
            '',
            'Kennisbank over deze producten:',
        ];

        foreach ($resolved as $item) {
            $product = $item['product'];

            if (! $product instanceof Product) {
                $lines[] = "- {$item['name']}: niet gevonden in de kennisbank. Behandel het voorzichtig en zeg dat het niet gecontroleerd is.";

                continue;
            }

            $status = $product->status instanceof ProductStatus ? $product->status->label() : $product->status;
            $lines[] = "- {$product->name} (status: {$status}; categorie: {$product->category?->name}): {$product->why}"
                .($product->conditions ? ' Voorwaarde: '.$product->conditions : '')
                .($product->notes ? ' Let op: '.$product->notes : '');
        }

        $allowed = Product::query()
            ->where('status', ProductStatus::Toegestaan)
            ->orderBy('name')
            ->limit(40)
            ->pluck('name')
            ->implode(', ');

        $lines[] = '';
        $lines[] = 'Voorbeelden van toegestane extra ingrediënten uit de kennisbank: '.$allowed.'.';
        $lines[] = 'Maak één dagmenu (ontbijt, lunch, diner en een tussendoortje) dat beide koelkastproducten gebruikt, tenzij een product niet toegestaan is. Dan een veilig alternatief voorstellen.';

        return implode("\n", $lines);
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Je bent de voedingsassistent van praktijk ARDRA voor de candidakuur.
Antwoord altijd in het Nederlands, kort en praktisch.
Gebruik alleen ingrediënten die bij dit dieet passen: geen suiker, geen gist, geen gewone zuivel, geen snelle koolhydraten, geen alcohol. Kook vers, kleine porties.
Volg de status uit de kennisbank strikt. Is iets niet toegestaan, verwerk het niet in het menu.
Verzin geen medische claims. Dit is voedingsadvies, geen diagnose.

Antwoord uitsluitend als JSON met deze velden:
{
  "title": "korte menunaam",
  "intro": "2 zinnen waarom dit past",
  "meals": [
    {"label": "Ontbijt", "text": "wat je eet", "note": "optionele tip of null"}
  ],
  "tips": ["korte tip"],
  "warning": "waarschuwing of null"
}
PROMPT;
    }

    /**
     * @return array{title: string, intro: string, meals: list<array{label: string, text: string, note: ?string}>, tips: list<string>, warning: ?string}
     */
    private function parse(string $content): array
    {
        $json = $content;

        if (preg_match('/\{.*\}/s', $content, $matches) === 1) {
            $json = $matches[0];
        }

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('De assistent gaf geen bruikbaar menu terug.');
        }

        $meals = [];
        foreach ($decoded['meals'] ?? [] as $meal) {
            if (! is_array($meal) || ! filled($meal['label'] ?? null) || ! filled($meal['text'] ?? null)) {
                continue;
            }

            $meals[] = [
                'label' => (string) $meal['label'],
                'text' => (string) $meal['text'],
                'note' => filled($meal['note'] ?? null) ? (string) $meal['note'] : null,
            ];
        }

        if ($meals === []) {
            throw new RuntimeException('De assistent gaf geen bruikbaar menu terug.');
        }

        $tips = [];
        foreach ($decoded['tips'] ?? [] as $tip) {
            if (is_string($tip) && trim($tip) !== '') {
                $tips[] = $tip;
            }
        }

        $warning = $decoded['warning'] ?? null;

        return [
            'title' => filled($decoded['title'] ?? null) ? (string) $decoded['title'] : 'Menu uit de koelkast',
            'intro' => filled($decoded['intro'] ?? null) ? (string) $decoded['intro'] : '',
            'meals' => $meals,
            'tips' => $tips,
            'warning' => is_string($warning) && trim($warning) !== '' ? $warning : null,
        ];
    }
}
