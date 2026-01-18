<div>
    <!-- Hero Section -->
    @if($movies->isNotEmpty())
        @php $featuredMovie = $movies->first(); @endphp
        <div class="relative h-[60vh] w-full overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=2525&auto=format&fit=crop')] bg-cover bg-center opacity-40"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-seatsync-bg via-seatsync-bg/50 to-transparent"></div>

            <div class="relative max-w-7xl mx-auto px-4 h-full flex items-end pb-20">
                <div class="max-w-2xl">
                    <span class="text-seatsync-gold font-bold tracking-widest uppercase text-sm mb-2 block">Now Showing</span>
                    <h1 class="text-5xl md:text-7xl font-heading mb-4 leading-tight text-white drop-shadow-2xl">{{ $featuredMovie->title }}</h1>
                    @if($featuredMovie->description)
                        <p class="text-gray-300 text-lg mb-8 line-clamp-2 max-w-xl">{{ $featuredMovie->description }}</p>
                    @endif
                    <div class="flex gap-4">
                        <a href="{{ route('screenings.show', ['movie' => $featuredMovie->id]) }}" class="bg-seatsync-gold hover:bg-seatsync-goldhover text-black px-8 py-3 rounded-sm font-bold uppercase tracking-wider flex items-center gap-2 transition-all shadow-glow">
                            <i data-lucide="ticket" class="w-4 h-4"></i> Get Tickets
                        </a>
                        <button class="border border-white/30 hover:bg-white/10 text-white px-8 py-3 rounded-sm font-bold uppercase tracking-wider transition-all">
                            Watch Trailer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Movie Grid -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-heading text-seatsync-gold border-l-4 border-seatsync-gold pl-4">Now Showing</h2>
            <div class="flex gap-2">
                <button class="p-2 border border-seatsync-border rounded-lg hover:border-seatsync-gold transition-colors"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                <button class="p-2 border border-seatsync-border rounded-lg hover:border-seatsync-gold transition-colors"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
            </div>
        </div>

        <!-- Search -->
        <div class="mb-8 max-w-md">
            <div class="relative">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    class="block w-full bg-seatsync-surface border border-seatsync-border rounded-lg px-4 py-3 text-white placeholder-seatsync-silver focus:border-seatsync-gold focus:ring-1 focus:ring-seatsync-gold transition-colors"
                    placeholder="Search movies..."
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i data-lucide="search" class="w-5 h-5 text-seatsync-silver"></i>
                </div>
            </div>
        </div>

        @if($movies->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($movies as $movie)
                    <!-- Movie Card -->
                    <div class="group relative bg-seatsync-surface rounded-xl overflow-hidden border border-seatsync-border hover:border-seatsync-gold/50 transition-all duration-300 cursor-pointer" onclick="window.location.href='{{ route('screenings.show', ['movie' => $movie->id]) }}'">
                        <div class="aspect-[2/3] overflow-hidden">
                            @if($movie->poster_path)
                                <img src="{{ asset('storage/' . $movie->poster_path) }}" alt="{{ $movie->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                            @else
                                <div class="w-full h-full bg-seatsync-border flex items-center justify-center">
                                    <i data-lucide="film" class="w-12 h-12 text-seatsync-silver"></i>
                                </div>
                            @endif
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent opacity-90"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5 translate-y-2 group-hover:translate-y-0 transition-transform">
                            <h3 class="text-xl font-heading text-white mb-1">{{ $movie->title }}</h3>
                            <div class="flex items-center gap-2 text-xs text-seatsync-silver mb-3">
                                <span class="bg-seatsync-border px-1.5 py-0.5 rounded">PG-13</span>
                                @if($movie->genres->isNotEmpty())
                                    <span>{{ $movie->genres->first()->name }}</span>
                                @endif
                                <span>•</span>
                                <span>{{ $movie->screenings->count() }} showings</span>
                            </div>
                            <button class="w-full bg-seatsync-gold text-black py-2 font-bold uppercase text-xs rounded-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                Select Seats
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-seatsync-surface rounded-full flex items-center justify-center mb-6">
                    <i data-lucide="film" class="w-12 h-12 text-seatsync-silver"></i>
                </div>
                <h3 class="text-2xl font-heading text-seatsync-gold mb-4">
                    @if($search !== '')
                        No movies found
                    @else
                        No movies available
                    @endif
                </h3>
                <p class="text-seatsync-silver text-lg mb-8">
                    @if($search !== '')
                        Try adjusting your search terms or browse all movies.
                    @else
                        Check back later for new movie releases.
                    @endif
                </p>
                @if($search !== '')
                    <button
                        wire:click="$set('search', '')"
                        class="bg-seatsync-gold text-black px-6 py-3 rounded-sm font-bold uppercase tracking-wider hover:bg-seatsync-goldhover transition-colors"
                    >
                        Clear Search
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>