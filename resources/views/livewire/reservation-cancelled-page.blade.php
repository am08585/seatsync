<div class="min-h-screen bg-seatsync-bg">
    <div class="mx-auto max-w-2xl px-4 py-16">
        <!-- Success header -->
        <div class="text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-seatsync-gold/10 border-2 border-seatsync-gold mb-6">
                <i data-lucide="check-circle" class="h-10 w-10 text-seatsync-gold"></i>
            </div>
            <h1 class="text-4xl font-heading text-white mb-4">Reservation Cancelled</h1>
            <p class="text-seatsync-silver text-lg">
                Your reservation has been successfully cancelled
            </p>
        </div>

        <!-- Cancellation details -->
        <div class="mt-12 bg-seatsync-surface rounded-xl border border-seatsync-border p-8">
            <h2 class="text-xl font-heading text-seatsync-gold border-l-4 border-seatsync-gold pl-4 mb-6">Cancellation Details</h2>

            <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center py-2 border-b border-seatsync-border/50">
                    <span class="text-seatsync-silver">Reservation Number:</span>
                    <span class="text-white font-bold">#{{ $reservation->id }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-seatsync-border/50">
                    <span class="text-seatsync-silver">Cancelled At:</span>
                    <span class="text-white">{{ $cancelledAt }}</span>
                </div>
            </div>

            <!-- Movie details -->
            <div class="bg-black/40 rounded-lg p-6 mb-6 border border-seatsync-border/50">
                <h3 class="text-seatsync-gold font-bold mb-4 flex items-center gap-2">
                    <i data-lucide="film" class="w-4 h-4"></i>
                    Movie Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-seatsync-silver">Movie:</span>
                        <span class="text-white font-medium">{{ $reservation->screening->movie->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-seatsync-silver">Theater:</span>
                        <span class="text-white">{{ $reservation->screening->theater->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-seatsync-silver">Showtime:</span>
                        <span class="text-white">{{ $reservation->screening->start_time->format('l, F j, Y \a\t g:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Released seats -->
            <div class="bg-seatsync-blue/10 border border-seatsync-blue/20 rounded-lg p-6 mb-6">
                <h3 class="text-seatsync-blue font-bold mb-4 flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    Seats Released
                </h3>
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($releasedSeats as $seatId)
                        @php
                            $seat = $reservation->seats->find($seatId);
                        @endphp
                        @if($seat)
                            <span class="bg-seatsync-blue/20 text-seatsync-blue px-3 py-1 rounded border border-seatsync-blue/30 text-sm">
                                Row {{ $seat->row }} - {{ $seat->number }}
                            </span>
                        @endif
                    @endforeach
                </div>
                <p class="text-seatsync-silver text-sm">
                    These seats are now available for other customers to reserve.
                </p>
            </div>

            <!-- Refund information -->
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <i data-lucide="credit-card" class="w-6 h-6 text-green-500 mt-0.5"></i>
                    <div>
                        <h3 class="text-green-500 font-bold mb-2">Refund Processed</h3>
                        <p class="text-seatsync-silver text-sm leading-relaxed">
                            Your refund of <span class="text-seatsync-gold font-bold">${{ number_format($reservation->total_price / 100, 2) }}</span> has been processed and will appear on your original payment method within 3-5 business days.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                <a
                    href="{{ route('reservations.index') }}"
                    class="flex-1 bg-seatsync-gold text-black py-3 rounded-sm font-bold uppercase tracking-wider hover:bg-seatsync-goldhover transition-colors text-center flex items-center justify-center gap-2"
                >
                    <i data-lucide="ticket" class="w-4 h-4"></i>
                    View My Reservations
                </a>
                <a
                    href="{{ route('movies.index') }}"
                    class="flex-1 border border-seatsync-border text-seatsync-silver py-3 rounded-sm font-bold uppercase tracking-wider hover:bg-seatsync-surface hover:text-white transition-colors text-center flex items-center justify-center gap-2"
                >
                    <i data-lucide="film" class="w-4 h-4"></i>
                    Browse Movies
                </a>
            </div>
        </div>
    </div>
</div>