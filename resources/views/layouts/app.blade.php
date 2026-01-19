<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SeatSync - Movie Theater' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-seatsync-bg font-body flex min-h-screen flex-col text-white antialiased">
    <!-- NAVIGATION BAR -->
    <nav class="bg-seatsync-bg/90 border-seatsync-border sticky top-0 z-50 border-b backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('movies.index') }}">
                    <div class="flex items-center gap-3">

                        <span
                            class="bg-seatsync-gold font-heading flex h-10 w-10 items-center justify-center rounded-full text-xl text-black">
                            S</span>
                        <span class="font-heading text-2xl tracking-wider text-white">SeatSync</span>

                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="{{ route('movies.index') }}"
                            class="hover:text-seatsync-gold {{ request()->routeIs('movies.*') ? 'text-seatsync-gold' : 'text-white' }} rounded-md px-3 py-2 text-sm font-bold uppercase tracking-wide transition-colors">Movies</a>
                        <a href="{{ route('reservations.index') }}"
                            class="hover:text-seatsync-gold {{ request()->routeIs('reservations.*') ? 'text-seatsync-gold' : 'text-white' }} rounded-md px-3 py-2 text-sm font-bold uppercase tracking-wide transition-colors">My
                            Tickets</a>
                        @auth
                            @if (auth()->user()->is_admin ?? false)
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="text-seatsync-silver rounded-md px-3 py-2 text-sm font-bold uppercase tracking-wide transition-colors hover:text-white">Admin</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- User Profile -->
                <div class="flex items-center gap-4">
                    {{-- <button class="text-seatsync-silver p-2 hover:text-white">
                        <i data-lucide="search" class="h-5 w-5"></i>
                    </button> --}}
                    @auth
                        <div class="flex items-center gap-3">
                            <span class="text-seatsync-silver hidden text-sm sm:block">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-seatsync-surface border-seatsync-border hover:border-seatsync-gold flex h-8 w-8 cursor-pointer items-center justify-center overflow-hidden rounded-full border transition-colors">
                                    <i data-lucide="log-out" class="text-seatsync-silver h-4 w-4"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <div
                            class="bg-seatsync-surface border-seatsync-border flex h-8 w-8 items-center justify-center overflow-hidden rounded-full border">
                            <i data-lucide="user" class="text-seatsync-silver h-5 w-5"></i>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    @livewireScripts

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>

</html>
