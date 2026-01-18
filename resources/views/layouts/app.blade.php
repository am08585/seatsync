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

<body class="bg-seatsync-bg text-white font-body antialiased min-h-screen flex flex-col">
    <!-- NAVIGATION BAR -->
    <nav class="sticky top-0 z-50 bg-seatsync-bg/90 backdrop-blur-md border-b border-seatsync-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-seatsync-gold rounded-full flex items-center justify-center text-black font-heading text-xl">S</div>
                    <span class="text-2xl font-heading tracking-wider text-white">SeatSync</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="{{ route('movies.index') }}" class="hover:text-seatsync-gold transition-colors px-3 py-2 rounded-md text-sm font-bold uppercase tracking-wide {{ request()->routeIs('movies.*') ? 'text-seatsync-gold' : 'text-white' }}">Movies</a>
                        <a href="{{ route('reservations.index') }}" class="hover:text-seatsync-gold transition-colors px-3 py-2 rounded-md text-sm font-bold uppercase tracking-wide {{ request()->routeIs('reservations.*') ? 'text-seatsync-gold' : 'text-white' }}">My Tickets</a>
                        @auth
                            @if(auth()->user()->is_admin ?? false)
                                <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-seatsync-silver hover:text-white transition-colors px-3 py-2 rounded-md text-sm font-bold uppercase tracking-wide">Admin</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- User Profile -->
                <div class="flex items-center gap-4">
                    <button class="p-2 text-seatsync-silver hover:text-white">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                    @auth
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-seatsync-silver hidden sm:block">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-full bg-seatsync-surface border border-seatsync-border flex items-center justify-center overflow-hidden hover:border-seatsync-gold transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4 text-seatsync-silver"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-full bg-seatsync-surface border border-seatsync-border flex items-center justify-center overflow-hidden">
                            <i data-lucide="user" class="w-5 h-5 text-seatsync-silver"></i>
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
