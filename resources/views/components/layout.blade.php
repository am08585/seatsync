@props(['title' => config('app.name', 'Laravel')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-seatsync-bg font-body text-white antialiased">
    <!-- Cinema Background Pattern -->
    <div class="opacity-3 pointer-events-none fixed inset-0">
        <div class="from-seatsync-gold/5 to-seatsync-blue/5 absolute inset-0 bg-gradient-to-br via-transparent"></div>
        <div class="grid h-full grid-cols-12 gap-2">
            @for ($i = 0; $i < 144; $i++)
                <div class="bg-seatsync-border/20 rounded-full"></div>
            @endfor
        </div>
    </div>

    <main class="relative flex min-h-screen flex-col items-center justify-center px-6 py-8">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>
    <script src="../path/to/flowbite/dist/flowbite.min.js"></script>

</body>

</html>
