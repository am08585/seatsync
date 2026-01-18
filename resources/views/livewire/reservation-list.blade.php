<div>
    <div class="max-w-4xl mx-auto px-4 py-12">
        <h2 class="text-3xl font-heading text-white mb-8">My Reservations</h2>

        @if($upcomingReservations->isNotEmpty() || $pastReservations->isNotEmpty())
            <div class="space-y-6">
                <!-- Upcoming Reservations -->
                @if($upcomingReservations->isNotEmpty())
                    @foreach($upcomingReservations as $reservation)
                        <div class="bg-seatsync-surface rounded-xl border border-seatsync-border overflow-hidden flex flex-col md:flex-row">
                            <div class="w-full md:w-48 h-48 md:h-auto relative">
                                @if($reservation->screening->movie->poster_path)
                                    <img src="{{ asset('storage/' . $reservation->screening->movie->poster_path) }}" class="w-full h-full object-cover" alt="{{ $reservation->screening->movie->title }}">
                                @else
                                    <div class="w-full h-full bg-seatsync-border flex items-center justify-center">
                                        <i data-lucide="film" class="w-12 h-12 text-seatsync-silver"></i>
                                    </div>
                                @endif
                                <div class="absolute top-2 left-2 bg-green-500 text-black text-xs font-bold px-2 py-1 rounded">CONFIRMED</div>
                            </div>
                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-2xl font-heading text-white mb-2">{{ $reservation->screening->movie->title }}</h3>
                                    <div class="flex flex-wrap gap-4 text-sm text-seatsync-silver mb-4">
                                        <div class="flex items-center gap-1"><i data-lucide="calendar" class="w-4 h-4"></i> {{ $reservation->screening->start_time->format('M j, Y') }}</div>
                                        <div class="flex items-center gap-1"><i data-lucide="clock" class="w-4 h-4"></i> {{ $reservation->screening->start_time->format('g:i A') }}</div>
                                        <div class="flex items-center gap-1"><i data-lucide="map-pin" class="w-4 h-4"></i> {{ $reservation->screening->theater->name }}</div>
                                    </div>
                                    <div class="flex gap-2 mb-4">
                                        @foreach($reservation->seats as $seat)
                                            <span class="bg-seatsync-border text-white text-xs px-2 py-1 rounded border border-seatsync-gold/50">
                                                Row {{ $seat->row }}, Seat {{ $seat->number }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex gap-4 pt-4 border-t border-seatsync-border">
                                    <button class="text-seatsync-gold text-sm font-bold uppercase hover:underline">View QR Code</button>
                                    <button wire:click="confirmCancel({{ $reservation->id }})" class="text-seatsync-red text-sm font-bold uppercase hover:underline ml-auto">Cancel Reservation</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Past Reservations -->
                @if($pastReservations->isNotEmpty())
                    @foreach($pastReservations as $reservation)
                        <div class="bg-seatsync-surface rounded-xl border border-seatsync-border overflow-hidden flex flex-col md:flex-row opacity-60">
                            <div class="w-full md:w-48 h-48 md:h-auto bg-seatsync-occupied flex items-center justify-center">
                                <i data-lucide="film" class="w-12 h-12 text-seatsync-silver"></i>
                            </div>
                            <div class="p-6 flex-1">
                                <h3 class="text-xl font-heading text-seatsync-silver">{{ $reservation->screening->movie->title }}</h3>
                                <p class="text-sm text-seatsync-silver/70 mt-1">
                                    @if($reservation->status === 'cancelled')
                                        Cancelled on {{ $reservation->cancelled_at?->format('M j, Y') }}
                                    @else
                                        Viewed on {{ $reservation->screening->start_time->format('M j, Y') }}
                                    @endif
                                </p>
                                <div class="mt-4">
                                    <span class="text-xs bg-seatsync-border px-2 py-1 rounded text-seatsync-silver">
                                        @if($reservation->status === 'cancelled')
                                            CANCELLED
                                        @else
                                            COMPLETED
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-seatsync-surface rounded-full flex items-center justify-center mb-6">
                    <i data-lucide="ticket" class="w-12 h-12 text-seatsync-silver"></i>
                </div>
                <h3 class="text-2xl font-heading text-seatsync-gold mb-4">No reservations yet</h3>
                <p class="text-seatsync-silver text-lg mb-8">
                    You haven't made any movie reservations yet. Start exploring movies to book your seats!
                </p>
                <a href="{{ route('movies.index') }}" class="bg-seatsync-gold text-black px-6 py-3 rounded-sm font-bold uppercase tracking-wider hover:bg-seatsync-goldhover transition-colors">
                    Browse Movies
                </a>
            </div>
        @endif
    </div>

    <!-- Include the cancel confirmation modal -->
    @livewire('reservation-cancel-confirm')
</div>