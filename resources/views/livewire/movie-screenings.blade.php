<div class="flex flex-col lg:flex-row h-[calc(100vh-80px)]">
    <!-- Left: Movie Info Sidebar -->
    <div class="w-full lg:w-80 bg-seatsync-surface border-r border-seatsync-border p-6 flex flex-col hidden lg:flex">
        @if($movie->poster_path)
            <img src="{{ asset('storage/' . $movie->poster_path) }}" class="w-full rounded-lg shadow-lg mb-6 opacity-90" alt="{{ $movie->title }}">
        @else
            <div class="w-full aspect-[2/3] bg-seatsync-border rounded-lg flex items-center justify-center mb-6">
                <i data-lucide="film" class="w-12 h-12 text-seatsync-silver"></i>
            </div>
        @endif
        <h2 class="text-2xl font-heading text-white mb-2">{{ $movie->title }}</h2>
        @if($movie->description)
            <p class="text-seatsync-silver text-sm mb-4">{{ $movie->description }}</p>
        @endif

        <!-- Genres -->
        @if($movie->genres->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($movie->genres as $genre)
                    <span class="bg-seatsync-border text-white text-xs px-2 py-1 rounded border border-seatsync-gold/50">
                        {{ $genre->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="mt-auto">
            <a href="{{ route('movies.index') }}" class="w-full bg-seatsync-gold text-black py-3 rounded-sm font-bold uppercase tracking-wider hover:bg-seatsync-goldhover transition-colors flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Movies
            </a>
        </div>
    </div>

    <!-- Right: Screenings List -->
    <div class="flex-1 bg-seatsync-bg overflow-y-auto">
        <div class="p-8">
            <!-- Mobile Movie Header -->
            <div class="lg:hidden mb-8">
                <div class="flex items-center gap-4 mb-4">
                    @if($movie->poster_path)
                        <img src="{{ asset('storage/' . $movie->poster_path) }}" class="w-16 h-24 rounded-lg object-cover" alt="{{ $movie->title }}">
                    @else
                        <div class="w-16 h-24 bg-seatsync-border rounded-lg flex items-center justify-center">
                            <i data-lucide="film" class="w-6 h-6 text-seatsync-silver"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-heading text-white">{{ $movie->title }}</h1>
                        @if($movie->genres->isNotEmpty())
                            <div class="flex gap-2 mt-1">
                                @foreach($movie->genres->take(2) as $genre)
                                    <span class="bg-seatsync-border text-seatsync-silver text-xs px-2 py-1 rounded">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <a href="{{ route('movies.index') }}" class="inline-flex items-center gap-2 text-seatsync-gold hover:text-white transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Movies
                </a>
            </div>

            @if($screenings->isNotEmpty())
                <div class="space-y-8">
                    @foreach($screenings as $date => $dayScreenings)
                        <div class="bg-seatsync-surface rounded-xl border border-seatsync-border overflow-hidden">
                            <div class="p-6 border-b border-seatsync-border">
                                <h2 class="text-xl font-heading text-seatsync-gold">
                                    {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                                </h2>
                            </div>

                            <div class="p-6 space-y-3">
                                @foreach($dayScreenings as $screening)
                                    <div class="flex items-center justify-between p-4 bg-black/20 rounded-lg border border-seatsync-border hover:border-seatsync-gold/50 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <!-- Theater Icon -->
                                            <div class="w-10 h-10 bg-seatsync-blue/20 rounded-lg flex items-center justify-center">
                                                <i data-lucide="map-pin" class="w-5 h-5 text-seatsync-blue"></i>
                                            </div>

                                            <!-- Screening Details -->
                                            <div>
                                                <h3 class="font-bold text-white">{{ $screening->theater->name }}</h3>
                                                <p class="text-seatsync-silver text-sm">{{ $screening->start_time->format('g:i A') }} • ${{ number_format($screening->base_price / 100, 2) }}</p>
                                            </div>
                                        </div>

                                        <!-- Book Button -->
                                        <button disabled class="bg-seatsync-gold text-black px-6 py-2 rounded-sm font-bold uppercase text-sm hover:bg-seatsync-goldhover transition-colors cursor-not-allowed">
                                            Coming Soon
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Screenings -->
                <div class="text-center py-16">
                    <div class="mx-auto w-24 h-24 bg-seatsync-surface rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="clock" class="w-12 h-12 text-seatsync-silver"></i>
                    </div>
                    <h3 class="text-2xl font-heading text-seatsync-gold mb-4">No upcoming showings</h3>
                    <p class="text-seatsync-silver text-lg mb-8">
                        There are currently no upcoming screenings for this movie. Check back later or browse other movies.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('movies.index') }}" class="bg-seatsync-gold text-black px-6 py-3 rounded-sm font-bold uppercase tracking-wider hover:bg-seatsync-goldhover transition-colors">
                            Browse Movies
                        </a>
                        <a href="{{ route('reservations.index') }}" class="border border-seatsync-border text-white px-6 py-3 rounded-sm font-bold uppercase tracking-wider hover:bg-seatsync-gold hover:border-seatsync-gold transition-colors">
                            My Tickets
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>