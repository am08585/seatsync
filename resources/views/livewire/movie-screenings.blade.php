<div class="flex h-[calc(100vh-80px)] flex-col lg:flex-row">
    <!-- Left: Movie Info Sidebar -->
    <div class="bg-seatsync-surface border-seatsync-border flex hidden w-full flex-col border-r p-6 lg:flex lg:w-80">
        @if ($movie->poster_path)
            <img src="{{ asset('storage/' . $movie->poster_path) }}" class="mb-6 w-full rounded-lg opacity-90 shadow-lg"
                alt="{{ $movie->title }}">
        @else
            <div class="bg-seatsync-border mb-6 flex aspect-[2/3] w-full items-center justify-center rounded-lg">
                <i data-lucide="film" class="text-seatsync-silver h-12 w-12"></i>
            </div>
        @endif
        <h2 class="font-heading mb-2 text-2xl text-white">{{ $movie->title }}</h2>
        @if ($movie->description)
            <p class="text-seatsync-silver mb-4 text-sm">{{ $movie->description }}</p>
        @endif

        <!-- Genres -->
        @if ($movie->genres->isNotEmpty())
            <div class="mb-6 flex flex-wrap gap-2">
                @foreach ($movie->genres as $genre)
                    <span
                        class="bg-seatsync-border border-seatsync-gold/50 rounded border px-2 py-1 text-xs text-white">
                        {{ $genre->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="mt-auto">
            <a href="{{ route('movies.index') }}"
                class="bg-seatsync-gold hover:bg-seatsync-goldhover flex w-full items-center justify-center gap-2 rounded-sm py-3 font-bold uppercase tracking-wider text-black transition-colors">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Movies
            </a>
        </div>
    </div>

    <!-- Right: Screenings List -->
    <div class="bg-seatsync-bg flex-1 overflow-y-auto">
        <div class="p-8">
            <!-- Mobile Movie Header -->
            <div class="mb-8 lg:hidden">
                <div class="mb-4 flex items-center gap-4">
                    @if ($movie->poster_path)
                        <img src="{{ asset('storage/' . $movie->poster_path) }}"
                            class="aspect-2/3 w-36 rounded-lg object-cover" alt="{{ $movie->title }}">
                    @else
                        <div class="bg-seatsync-border flex h-24 w-16 items-center justify-center rounded-lg">
                            <i data-lucide="film" class="text-seatsync-silver h-6 w-6"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="font-heading text-2xl text-white">{{ $movie->title }}</h1>
                        @if ($movie->genres->isNotEmpty())
                            <div class="mt-1 flex gap-2">
                                @foreach ($movie->genres->take(2) as $genre)
                                    <span
                                        class="bg-seatsync-border text-seatsync-silver rounded px-2 py-1 text-xs">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        <p class="my-4 line-clamp-2 max-w-xl text-lg text-gray-300">{{ $movie->description }}</p>
                    </div>
                </div>
                <a href="{{ route('movies.index') }}"
                    class="text-seatsync-gold inline-flex items-center gap-2 transition-colors hover:text-white">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i> Back to Movies
                </a>
            </div>

            @if ($screenings->isNotEmpty())
                <div class="space-y-8">
                    @foreach ($screenings as $date => $dayScreenings)
                        <div class="bg-seatsync-surface border-seatsync-border overflow-hidden rounded-xl border">
                            <div class="border-seatsync-border border-b p-6">
                                <h2 class="font-heading text-seatsync-gold text-xl">
                                    {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                                </h2>
                            </div>

                            <div class="space-y-3 p-6">
                                @foreach ($dayScreenings as $screening)
                                    <div
                                        class="border-seatsync-border hover:border-seatsync-gold/50 flex items-center justify-between rounded-lg border bg-black/20 p-4 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <!-- Theater Icon -->
                                            <div
                                                class="bg-seatsync-blue/20 flex h-10 w-10 items-center justify-center rounded-lg">
                                                <i data-lucide="map-pin" class="text-seatsync-blue h-5 w-5"></i>
                                            </div>

                                            <!-- Screening Details -->
                                            <div>
                                                <h3 class="font-bold text-white">{{ $screening->theater->name }}</h3>
                                                <p class="text-seatsync-silver text-sm">
                                                    {{ $screening->start_time->format('g:i A') }} •
                                                    ${{ number_format($screening->base_price / 100, 2) }}</p>
                                            </div>
                                        </div>

                                        <!-- Book Button -->
                                        <a href="{{ route('seat-selection.show', ['screening' => $screening->id]) }}"
                                            class="bg-seatsync-gold hover:bg-seatsync-goldhover flex items-center gap-2 rounded-sm px-6 py-2 text-sm font-bold uppercase text-black transition-colors">
                                            <i data-lucide="ticket" class="h-4 w-4"></i> Select Seats
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Screenings -->
                <div class="py-16 text-center">
                    <div
                        class="bg-seatsync-surface mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full">
                        <i data-lucide="clock" class="text-seatsync-silver h-12 w-12"></i>
                    </div>
                    <h3 class="font-heading text-seatsync-gold mb-4 text-2xl">No upcoming showings</h3>
                    <p class="text-seatsync-silver mb-8 text-lg">
                        There are currently no upcoming screenings for this movie. Check back later or browse other
                        movies.
                    </p>
                    <div class="flex flex-col justify-center gap-4 sm:flex-row">
                        <a href="{{ route('movies.index') }}"
                            class="bg-seatsync-gold hover:bg-seatsync-goldhover rounded-sm px-6 py-3 font-bold uppercase tracking-wider text-black transition-colors">
                            Browse Movies
                        </a>
                        <a href="{{ route('reservations.index') }}"
                            class="border-seatsync-border hover:bg-seatsync-gold hover:border-seatsync-gold rounded-sm border px-6 py-3 font-bold uppercase tracking-wider text-white transition-colors">
                            My Tickets
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
