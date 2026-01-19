<div>
    <div class="mx-auto max-w-4xl px-4 py-12">
        <h2 class="font-heading mb-8 text-3xl text-white">My Reservations</h2>

        @if ($upcomingReservations->isNotEmpty() || $pastReservations->isNotEmpty())
            <div class="space-y-6">
                <!-- Upcoming Reservations -->
                @if ($upcomingReservations->isNotEmpty())
                    @foreach ($upcomingReservations as $reservation)
                        <div
                            class="bg-seatsync-surface border-seatsync-border flex flex-col overflow-hidden rounded-xl border md:flex-row">
                            <div class="relative h-48 w-full md:h-auto md:w-48">
                                @if ($reservation->screening->movie->poster_path)
                                    <img src="{{ asset('storage/' . $reservation->screening->movie->poster_path) }}"
                                        class="h-full w-full object-cover"
                                        alt="{{ $reservation->screening->movie->title }}">
                                @else
                                    <div class="bg-seatsync-border flex h-full w-full items-center justify-center">
                                        <i data-lucide="film" class="text-seatsync-silver h-12 w-12"></i>
                                    </div>
                                @endif
                                <div
                                    class="@if ($reservation->cancelled_at) bg-red-600 text-black text-xs font-bold px-2 py-1 rounded @else bg-green-500 text-black text-xs font-bold px-2 py-1 rounded @endif absolute left-2 top-2">
                                    @if ($reservation->cancelled_at)
                                        CANCELLED
                                    @else
                                        CONFIRMED
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col justify-between p-6">
                                <div>
                                    <h3 class="font-heading mb-2 text-2xl text-white">
                                        {{ $reservation->screening->movie->title }}</h3>
                                    <div class="text-seatsync-silver mb-4 flex flex-wrap gap-4 text-sm">
                                        <div class="flex items-center gap-1"><i data-lucide="calendar"
                                                class="h-4 w-4"></i>
                                            {{ $reservation->screening->start_time->format('M j, Y') }}</div>
                                        <div class="flex items-center gap-1"><i data-lucide="clock" class="h-4 w-4"></i>
                                            {{ $reservation->screening->start_time->format('g:i A') }}</div>
                                        <div class="flex items-center gap-1"><i data-lucide="map-pin"
                                                class="h-4 w-4"></i> {{ $reservation->screening->theater->name }}</div>
                                    </div>
                                    <div class="mb-4 flex gap-2">
                                        @foreach ($reservation->seats as $seat)
                                            <span
                                                class="bg-seatsync-border border-seatsync-gold/50 rounded border px-2 py-1 text-xs text-white">
                                                Row {{ $seat->row }}, Seat {{ $seat->number }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="border-seatsync-border flex gap-4 border-t pt-4">
                                    @if (!$reservation->cancelled_at)
                                        <button
                                            class="text-seatsync-gold text-sm font-bold uppercase hover:underline">View
                                            QR Code</button>
                                        <button wire:click="confirmCancel({{ $reservation->id }})"
                                            class="text-seatsync-red ml-auto text-sm font-bold uppercase hover:underline">Cancel
                                            Reservation</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Past Reservations -->
                @if ($pastReservations->isNotEmpty())
                    @foreach ($pastReservations as $reservation)
                        <div
                            class="bg-seatsync-surface border-seatsync-border flex flex-col overflow-hidden rounded-xl border opacity-60 md:flex-row">
                            <div
                                class="bg-seatsync-occupied flex h-48 w-full items-center justify-center md:h-auto md:w-48">
                                <i data-lucide="film" class="text-seatsync-silver h-12 w-12"></i>
                            </div>
                            <div class="flex-1 p-6">
                                <h3 class="font-heading text-seatsync-silver text-xl">
                                    {{ $reservation->screening->movie->title }}</h3>
                                <p class="text-seatsync-silver/70 mt-1 text-sm">
                                    @if ($reservation->status === 'cancelled')
                                    @else
                                        Viewed on {{ $reservation->screening->start_time->format('M j, Y') }}
                                    @endif
                                </p>
                                <div class="mt-4">
                                    <span class="bg-seatsync-border text-seatsync-silver rounded px-2 py-1 text-xs">
                                        @if ($reservation->cancelled_at)
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
            <div class="py-16 text-center">
                <div class="bg-seatsync-surface mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full">
                    <i data-lucide="ticket" class="text-seatsync-silver h-12 w-12"></i>
                </div>
                <h3 class="font-heading text-seatsync-gold mb-4 text-2xl">No reservations yet</h3>
                <p class="text-seatsync-silver mb-8 text-lg">
                    You haven't made any movie reservations yet. Start exploring movies to book your seats!
                </p>
                <a href="{{ route('movies.index') }}"
                    class="bg-seatsync-gold hover:bg-seatsync-goldhover rounded-sm px-6 py-3 font-bold uppercase tracking-wider text-black transition-colors">
                    Browse Movies
                </a>
            </div>
        @endif
    </div>

    <!-- Include the cancel confirmation modal -->
    @livewire('reservation-cancel-confirm')
</div>
