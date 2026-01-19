<div>
    <!-- Hero Section -->
    @if ($movies->isNotEmpty())
        {{-- @php $featuredMovie = $movies->first(); @endphp --}}
        @php $featuredMovie = $movies->firstWhere('title', 'Dune: Part Two'); @endphp
        <div class="relative h-[60vh] min-w-full overflow-hidden">
            <img class="absolute inset-0 bg-cover bg-center opacity-40"
                src="{{ asset('storage/movies/posters/dune-2-movie-poster.jpg') }}">
            <div class="from-seatsync-bg via-seatsync-bg/50 bg-linear-to-t absolute inset-0 to-transparent"></div>


            <div class="relative mx-auto flex h-full max-w-7xl items-end px-4 pb-20">
                <div class="max-w-2xl">
                    <span class="text-seatsync-gold mb-1 block text-sm font-bold uppercase tracking-widest">Now
                        Showing</span>
                    <h1 class="font-heading mb-2 text-5xl leading-tight text-white drop-shadow-2xl md:text-7xl">
                        {{ $featuredMovie->title }}</h1>
                    @if ($featuredMovie->description)
                        <p class="mb-8 line-clamp-2 max-w-xl text-lg text-gray-300">{{ $featuredMovie->description }}</p>
                    @endif
                    <div class="flex gap-4">
                        <a href="{{ route('screenings.show', ['movie' => $featuredMovie->id]) }}"
                            class="bg-seatsync-gold hover:bg-seatsync-bg shadow-glow flex items-center gap-2 rounded-sm px-8 py-3 font-bold uppercase tracking-wider text-black transition-all hover:text-white">
                            <i data-lucide="ticket" class="h-4 w-4"></i> Get Tickets
                        </a>
                        <a href="https://youtu.be/Way9Dexny3w?si=i5aAE6Evq5lCIVjo" target="_blank"
                            class="rounded-sm border border-white/30 px-8 py-3 font-bold uppercase tracking-wider text-white transition-all hover:bg-white/10">
                            Watch Trailer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Movie Grid -->
    <div class="mx-auto max-w-7xl px-4 py-16">
        <div class="mb-8 flex items-center justify-between">
            <h2 class="font-heading text-seatsync-gold border-seatsync-gold border-l-4 pl-4 text-2xl">Now Showing</h2>
            {{-- <div class="flex gap-2">
                <button
                    class="border-seatsync-border hover:border-seatsync-gold rounded-lg border p-2 transition-colors"><i
                        data-lucide="chevron-left" class="h-4 w-4"></i></button>
                <button
                    class="border-seatsync-border hover:border-seatsync-gold rounded-lg border p-2 transition-colors"><i
                        data-lucide="chevron-right" class="h-4 w-4"></i></button>
            </div> --}}
        </div>

        <!-- Search -->
        <div class="mb-8 max-w-md">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="bg-seatsync-surface border-seatsync-border placeholder-seatsync-silver focus:border-seatsync-gold focus:ring-seatsync-gold block w-full rounded-lg border px-4 py-3 text-white transition-colors focus:ring-1"
                    placeholder="Search movies...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i data-lucide="search" class="text-seatsync-silver h-5 w-5"></i>
                </div>
            </div>
        </div>

        @if ($movies->isNotEmpty())
            <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                @foreach ($movies as $movie)
                    <!-- Movie Card -->
                    <div class="bg-seatsync-surface border-seatsync-border hover:border-seatsync-gold/50 group relative cursor-pointer overflow-hidden rounded-xl border transition-all duration-300"
                        onclick="window.location.href='{{ route('screenings.show', ['movie' => $movie->id]) }}'">
                        <div class="aspect-[2/3] overflow-hidden">
                            @if ($movie->poster_path)
                                <img src="{{ asset('storage/' . $movie->poster_path) }}" alt="{{ $movie->title }}"
                                    class="h-full w-full object-cover opacity-80 transition-transform duration-500 group-hover:scale-105 group-hover:opacity-100">
                            @else
                                <div class="bg-seatsync-border flex h-full w-full items-center justify-center">
                                    <i data-lucide="film" class="text-seatsync-silver h-12 w-12"></i>
                                </div>
                            @endif
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent opacity-90">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 translate-y-2 p-5 transition-transform group-hover:translate-y-0">
                            <h3 class="font-heading mb-1 text-xl text-white">{{ $movie->title }}</h3>
                            <div class="text-seatsync-silver mb-3 flex items-center gap-2 text-xs">
                                <span class="bg-seatsync-border rounded px-1.5 py-0.5">PG-13</span>
                                @if ($movie->genres->isNotEmpty())
                                    <span>{{ $movie->genres->first()->name }}</span>
                                @endif
                                <span>•</span>
                                <span>{{ $movie->screenings->count() }} showings</span>
                            </div>
                            <button
                                class="bg-seatsync-gold w-full cursor-pointer rounded-sm py-2 text-xs font-bold uppercase text-black opacity-0 transition-opacity group-hover:opacity-100">
                                Select Seats
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="py-16 text-center">
                <div
                    class="bg-seatsync-surface mx-auto mb-6 flex h-24 w-24 cursor-pointer items-center justify-center rounded-full">
                    <i data-lucide="film" class="text-seatsync-silver h-12 w-12"></i>
                </div>
                <h3 class="font-heading text-seatsync-gold mb-4 text-2xl">
                    @if ($search !== '')
                        No movies found
                    @else
                        No movies available
                    @endif
                </h3>
                <p class="text-seatsync-silver mb-8 text-lg">
                    @if ($search !== '')
                        Try adjusting your search terms or browse all movies.
                    @else
                        Check back later for new movie releases.
                    @endif
                </p>
                @if ($search !== '')
                    <button wire:click="$set('search', '')"
                        class="bg-seatsync-gold hover:bg-seatsync-goldhover rounded-sm px-6 py-3 font-bold uppercase tracking-wider text-black transition-colors">
                        Clear Search
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
