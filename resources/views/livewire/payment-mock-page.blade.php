<main class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-800">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="mb-2 text-4xl font-bold text-white">Payment</h1>
            <p class="text-seatsync-silver">Complete your reservation</p>
        </div>

        @if ($isExpired)
            <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-red-400">
                <div class="flex items-center gap-3">
                    <i data-lucide="alert-circle" class="h-5 w-5"></i>
                    <div>
                        <h3 class="font-semibold">Hold Expired</h3>
                        <p class="text-sm">This seat hold has expired. Please select your seats again.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-6 rounded-lg border border-blue-500/30 bg-blue-500/10 p-4 text-blue-400">
                <div class="flex items-center gap-3">
                    <i data-lucide="info" class="h-5 w-5"></i>
                    <div>
                        <h3 class="font-semibold">Mock Payment</h3>
                        <p class="text-sm">Choose a simulated payment outcome below for testing purposes.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-3">
            <!-- Movie Details -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Screening Info -->
                <div class="bg-seatsync-surface border-seatsync-border rounded-lg border p-6">
                    <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-white">
                        <i data-lucide="film" class="text-seatsync-gold h-5 w-5"></i>
                        Movie Details
                    </h2>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="h-16 w-12 overflow-hidden rounded">
                                @if ($screening->movie?->poster_path)
                                    <img src="{{ asset('storage/' . $screening->movie->poster_path) }}"
                                        alt="{{ $screening->movie->title }}" class="h-full w-full object-cover">
                                @else
                                    <div class="bg-seatsync-border flex h-full w-full items-center justify-center">
                                        <i data-lucide="image" class="text-seatsync-silver h-4 w-4"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">{{ $screening->movie?->title ?? 'N/A' }}
                                </h3>
                                <p class="text-seatsync-silver text-sm">{{ $screening->movie?->genre ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="border-seatsync-border grid grid-cols-2 gap-4 border-t pt-3">
                            <div>
                                <span class="text-seatsync-silver text-sm">Theater</span>
                                <p class="font-medium text-white">{{ $screening->theater?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-seatsync-silver text-sm">Date & Time</span>
                                <p class="font-medium text-white">
                                    {{ $screening->start_time?->format('M j, Y - g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Seats -->
                <div class="bg-seatsync-surface border-seatsync-border rounded-lg border p-6">
                    <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-white">
                        <i data-lucide="ticket" class="text-seatsync-gold h-5 w-5"></i>
                        Selected Seats
                    </h2>

                    <div class="space-y-2">
                        @foreach ($seats as $seat)
                            <div class="flex items-center justify-between rounded-lg bg-black/30 p-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="bg-seatsync-gold flex h-8 w-8 items-center justify-center rounded text-xs font-bold text-black">
                                        {{ $seat->number }}
                                    </div>
                                    <div>
                                        <span class="font-medium text-white">Row {{ $seat->row }}, Seat
                                            {{ $seat->number }}</span>
                                        <span
                                            class="text-seatsync-silver ml-2 text-sm">{{ ucfirst($seat->seat_type) }}</span>
                                    </div>
                                </div>
                                <div class="text-seatsync-gold font-semibold">
                                    ${{ number_format($seatPriceCents($seat) / 100, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="space-y-6">
                <!-- Price Summary -->
                <div class="bg-seatsync-surface border-seatsync-border rounded-lg border p-6">
                    <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-white">
                        <i data-lucide="credit-card" class="text-seatsync-gold h-5 w-5"></i>
                        Payment Summary
                    </h2>

                    <div class="space-y-3">
                        <div class="text-seatsync-silver flex justify-between">
                            <span>Subtotal ({{ $seats->count() }} seats)</span>
                            <span>${{ number_format($totalCents / 100, 2) }}</span>
                        </div>
                        <div class="text-seatsync-silver flex justify-between">
                            <span>Processing Fee</span>
                            <span>$1.50</span>
                        </div>
                        <div class="text-seatsync-silver flex justify-between">
                            <span>Tax</span>
                            <span>${{ number_format(($totalCents * 0.08) / 100, 2) }}</span>
                        </div>
                        <div class="border-seatsync-border border-t pt-3">
                            <div class="flex justify-between text-lg font-bold text-white">
                                <span>Total</span>
                                <span
                                    class="text-seatsync-gold">${{ number_format(($totalCents + 150 + $totalCents * 0.08) / 100, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mock Payment Form -->
                <div class="bg-seatsync-surface border-seatsync-border rounded-lg border p-6">
                    <h2 class="mb-4 text-xl font-bold text-white">Payment Method</h2>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-seatsync-silver text-sm">Card Number</label>
                            <input type="text" placeholder="4242 4242 4242 4242"
                                class="border-seatsync-border placeholder-seatsync-silver focus:border-seatsync-gold w-full rounded-lg border bg-black/30 px-4 py-2 text-white focus:outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-seatsync-silver text-sm">Expiry</label>
                                <input type="text" placeholder="MM/YY"
                                    class="border-seatsync-border placeholder-seatsync-silver focus:border-seatsync-gold w-full rounded-lg border bg-black/30 px-4 py-2 text-white focus:outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-seatsync-silver text-sm">CVV</label>
                                <input type="text" placeholder="123"
                                    class="border-seatsync-border placeholder-seatsync-silver focus:border-seatsync-gold w-full rounded-lg border bg-black/30 px-4 py-2 text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <form method="POST" action="{{ route('payment.mock.success', ['hold_token' => $hold_token]) }}">
                        @csrf
                        <button type="submit" @disabled($isExpired)
                            class="bg-seatsync-gold hover:bg-seatsync-goldhover w-full rounded-lg px-6 py-3 font-semibold uppercase tracking-wider text-black transition-colors disabled:cursor-not-allowed disabled:opacity-50">
                            <i data-lucide="check-circle" class="mr-2 inline h-4 w-4"></i>
                            Simulate Success
                        </button>
                    </form>

                    <form method="POST" action="{{ route('payment.mock.fail', ['hold_token' => $hold_token]) }}">
                        @csrf
                        <button type="submit" @disabled($isExpired)
                            class="w-full rounded-lg bg-red-500 px-6 py-3 font-semibold uppercase tracking-wider text-white transition-colors hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                            <i data-lucide="x-circle" class="mr-2 inline h-4 w-4"></i>
                            Simulate Failure
                        </button>
                    </form>

                    <a href="{{ route('seat-selection.show', ['screening' => $screening->id]) }}"
                        class="text-seatsync-silver block w-full text-center transition-colors hover:text-white">
                        <i data-lucide="arrow-left" class="mr-2 inline h-4 w-4"></i>
                        Back to Seat Selection
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
