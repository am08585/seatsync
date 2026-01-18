<div>
@if($showModal && $reservation)
    <!-- Modal backdrop -->
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 backdrop-blur-sm">
        <div class="relative w-full max-w-md max-h-full m-4">
            <!-- Modal content -->
            <div class="relative rounded-xl bg-seatsync-surface border border-seatsync-border shadow-2xl">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-seatsync-border p-6">
                    <h3 class="text-xl font-heading text-white">
                        Cancel Reservation
                    </h3>
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="w-8 h-8 rounded-lg bg-seatsync-border flex items-center justify-center text-seatsync-silver hover:text-white hover:bg-seatsync-border/80 transition-colors"
                    >
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="p-6">
                    <div class="mb-6">
                        <p class="text-seatsync-silver text-sm mb-6 leading-relaxed">
                            Are you sure you want to cancel this reservation? This action cannot be undone.
                        </p>

                        <!-- Reservation Details -->
                        <div class="bg-black/40 rounded-lg p-4 border border-seatsync-border/50 mb-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-seatsync-silver text-sm">Movie:</span>
                                    <span class="text-white font-medium">{{ $reservation->screening->movie->title }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-seatsync-silver text-sm">Date & Time:</span>
                                    <span class="text-white">{{ $reservation->screening->start_time->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-seatsync-silver text-sm">Theater:</span>
                                    <span class="text-white">{{ $reservation->screening->theater->name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-seatsync-silver text-sm">Seats:</span>
                                    <span class="text-white">
                                        {{ $reservation->seats->map(fn($seat) => "Row {$seat->row}-{$seat->number}")->join(', ') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-seatsync-border">
                                    <span class="text-seatsync-gold font-bold">Total:</span>
                                    <span class="text-seatsync-gold font-bold">${{ number_format($reservation->total_price / 100, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Warning about cancellation policy -->
                        <div class="bg-seatsync-red/10 border border-seatsync-red/20 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-seatsync-red mt-0.5 flex-shrink-0"></i>
                                <div>
                                    <p class="text-seatsync-red text-sm font-medium mb-1">Important Notice</p>
                                    <p class="text-seatsync-silver text-sm">
                                        Cancellations are only allowed for confirmed reservations more than 60 minutes before the screening starts.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Error messages -->
                        @if($errors->has('authorization'))
                            <div class="mt-4 bg-seatsync-red/10 border border-seatsync-red/20 rounded-lg p-3">
                                <p class="text-seatsync-red text-sm">{{ $errors->first('authorization') }}</p>
                            </div>
                        @endif

                        @if($errors->has('validation'))
                            <div class="mt-4 bg-seatsync-red/10 border border-seatsync-red/20 rounded-lg p-3">
                                <p class="text-seatsync-red text-sm">{{ $errors->first('validation') }}</p>
                            </div>
                        @endif

                        @if($errors->has('general'))
                            <div class="mt-4 bg-seatsync-red/10 border border-seatsync-red/20 rounded-lg p-3">
                                <p class="text-seatsync-red text-sm">{{ $errors->first('general') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="flex items-center justify-end gap-3 border-t border-seatsync-border p-6">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="px-6 py-2.5 text-sm font-bold uppercase tracking-wider text-seatsync-silver hover:text-white border border-seatsync-border rounded-sm hover:bg-seatsync-border/50 transition-colors"
                    >
                        Keep Reservation
                    </button>
                    <button
                        type="button"
                        wire:click="cancelReservation"
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 text-sm font-bold uppercase tracking-wider bg-seatsync-red hover:bg-seatsync-red/80 text-white rounded-sm transition-colors flex items-center gap-2"
                        @disabled($isProcessing)
                    >
                        <span wire:loading.remove>
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Cancel Reservation
                        </span>
                        <span wire:loading>
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
</div>