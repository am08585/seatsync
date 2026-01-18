<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white">
    <main class="mx-auto w-full max-w-3xl px-6 py-8">
        {{ $slot }}
    </main>

    @livewireScripts
</body>

</html>
