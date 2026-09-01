<!DOCTYPE html>
<html lang="nl" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Voedingsadvies') — ARDRA</title>
    <meta name="description" content="@yield('description', 'Doorzoekbare kennisbank: wat mag je wel en niet eten tijdens het anti-candidadieet van praktijk ARDRA.')">
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans bg-paper text-ink">
    @include('partials.header')
    <main class="flex-1">
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>
