<x-mail::message>
# Gefeliciteerd{{ $user->name ? ', '.$user->name : '' }}

Plus is actief op Candidakuur. Fijn dat je meedoet.

**Dit kun je nu:**
- Onbeperkte koelkast-assistent
- Dagboek voor stemming, energie en klachten
- Volledige Wat nu?-kaarten

Actief tot **{{ $plusUntil->timezone(config('app.timezone'))->format('d-m-Y') }}**.

<x-mail::button :url="route('assistant')">
Open de assistent
</x-mail::button>

Of ga naar je [dagboek]({{ route('diary.index') }}) of [Wat nu?]({{ route('symptoms.index') }}).

Fijne kuur,<br>
{{ config('app.name') }}
</x-mail::message>
