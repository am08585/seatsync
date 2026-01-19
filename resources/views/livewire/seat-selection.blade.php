<div class="bg-seatsync-bg min-h-screen">
    <!-- Header -->
    <header class="bg-seatsync-surface border-seatsync-border border-b">
        <div class="mx-auto max-w-7xl px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex flex-row items-center gap-4">
                    <a href="{{ route('movies.index') }}" class="text-seatsync-gold transition-colors hover:text-white">
                        <i data-lucide="arrow-left" class="h-5 w-5"></i>
                        <span class="font-semibold">Back to {{ $screening->movie->title }}</span>
                    </a>
                </div>

                <h1 class="text-xl font-bold text-white">Select Seats</h1>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="mx-auto max-w-7xl px-4 py-8">
        <!-- Screening Info -->
        <div class="bg-seatsync-surface border-seatsync-border mb-8 rounded-xl border p-6">
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <h2 class="mb-2 text-lg font-semibold text-white">{{ $screening->movie->title }}</h2>
                    <p class="text-seatsync-silver">{{ $screening->start_time->format('l, F j, Y') }}</p>
                </div>
                <div>
                    <h3 class="mb-2 text-white">{{ $screening->theater->name }}</h3>
                    <p class="text-seatsync-silver text-sm">{{ $screening->start_time->format('g:i A') }}</p>
                    <p class="text-seatsync-gold font-bold">Base Price:
                        ${{ number_format($screening->base_price / 100, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Seat Map -->
        <div class="bg-seatsync-surface border-seatsync-border mb-8 rounded-xl border p-6">
            <div class="mb-6">
                <h3 class="mb-4 text-lg font-semibold text-white">Choose Your Seats</h3>
                <div class="text-seatsync-silver flex items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded bg-green-500"></div>
                        <span>Available</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded bg-gray-500"></div>
                        <span>Held</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded bg-red-500"></div>
                        <span>Reserved</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded bg-blue-500"></div>
                        <span>Selected</span>
                    </div>
                </div>
            </div>

            <!-- Theater Screen -->
            <div class="mb-12">
                <div class="relative">
                    <!-- Screen Frame -->
                    <div class="bg-linear-to-b h-4 rounded-t-3xl from-gray-800 to-gray-900 shadow-2xl"></div>
                    <div class="bg-linear-to-b h-2 rounded-t-2xl from-gray-900 to-black"></div>

                    <!-- Screen Surface -->
                    <div
                        class="bg-linear-to-b relative h-16 overflow-hidden rounded-t-2xl from-gray-100 to-gray-200 shadow-inner">
                        <div class="bg-linear-to-t absolute inset-0 from-transparent via-white/10 to-transparent">
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-seatsync-surface text-sm font-semibold tracking-widest">SCREEN</span>
                        </div>
                    </div>

                    <!-- Screen Glow Effect -->
                    <div
                        class="bg-linear-to-t absolute -bottom-4 left-1/2 h-8 w-3/4 -translate-x-1/2 transform from-blue-100/20 to-transparent blur-xl">
                    </div>
                </div>
            </div>

            <!-- Theater Seating Area -->
            <div class="relative mx-auto max-w-4xl">
                <div class="overflow-x-auto py-4">
                    <div class="inline-block min-w-full">
                        @foreach ($theaterSeats as $row => $seats)
                            <div class="mb-4 flex items-center justify-center">
                                <!-- Row Label Left -->
                                <div class="text-seatsync-silver mr-4 w-10 text-center text-sm font-bold">
                                    {{ $row }}
                                </div>

                                <!-- Seats Container -->
                                <div class="relative flex gap-2">
                                    @foreach ($seats as $seat)
                                        @php
                                            $status = $this->getSeatStatus($seat);
                                            $isSelected = in_array($seat->id, array_column($selectedSeats, 'seat_id'));

                                            // Add spacing between seat groups
                                            $seatGroupSpacing = '';
                                            if ($seat->number % 4 === 0 && $seat->number !== count($seats)) {
                                                $seatGroupSpacing = 'mr-4';
                                            }
                                        @endphp

                                        <div wire:click="toggleSeatSelection({{ $seat->id }})"
                                            class="@if ($isSelected) bg-blue-500 text-white shadow-lg ring-2 ring-blue-300 scale-105 z-10
                                            @elseif ($status === 'selected_held') bg-yellow-500 text-white shadow-lg ring-2 ring-yellow-300 scale-105 z-10
                                            @elseif ($status === 'held') bg-gray-700 text-white shadow-lg ring-2 ring-red-300 scale-105 z-10
                                            @elseif($status === 'reserved')
                                                bg-red-500 text-white cursor-not-allowed opacity-75
                                            @elseif($seat->seat_type === 'wheelchair')
                                                bg-gradient-to-br from-green-100 to-green-200 text-green-800 hover:from-green-200 hover:to-green-300 shadow-md
                                            @elseif($seat->seat_type === 'reduced_mobility')
                                                bg-gradient-to-br from-yellow-100 to-yellow-200 text-yellow-800 hover:from-yellow-200 hover:to-yellow-300 shadow-md
                                            @elseif($seat->seat_type === 'premium')
                                                bg-gradient-to-br from-blue-100 to-blue-200 text-blue-800 hover:from-blue-200 hover:to-blue-300 shadow-md
                                            @elseif($seat->seat_type === 'vip')
                                                bg-gradient-to-br from-purple-100 to-purple-200 text-purple-800 hover:from-purple-200 hover:to-purple-300 shadow-md
                                            @else
                                                bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800 hover:from-gray-200 hover:to-gray-300 shadow-sm @endif {{ $seatGroupSpacing }} flex h-10 w-10 transform cursor-pointer items-center justify-center rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-110"
                                            title="Seat {{ $seat->row }}{{ $seat->number }} - {{ ucfirst($seat->seat_type) }} (${{ $seat->price_modifier }}%)">
                                            {{ $seat->number }}
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Row Label Right -->
                                <div class="text-seatsync-silver ml-4 w-10 text-center text-sm font-bold">
                                    {{ $row }}
                                </div>
                            </div>

                            <!-- Add extra spacing between row groups (every 3 rows) -->
                            @php
                                $rowIndex = array_keys($theaterSeats->toArray());
                                $currentRowIndex = array_search($row, $rowIndex);
                                if (($currentRowIndex + 1) % 3 === 0 && $currentRowIndex !== count($rowIndex) - 1);
                            @endphp
                            @if (($currentRowIndex + 1) % 3 === 0 && $currentRowIndex !== count($rowIndex) - 1)
                                <div class="border-seatsync-border/30 my-2 h-6 border-t"></div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Aisle Indicators -->
                <div class="pointer-events-none absolute inset-0">
                    <div class="bg-seatsync-border/20 left-1/3 h-full w-px"></div>
                    <div class="bg-seatsync-border/20 right-1/3 h-full w-px"></div>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-8 grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-xs">Std</div>
                    <span class="text-seatsync-silver">Standard</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded bg-blue-100 text-xs">Prm</div>
                    <span class="text-seatsync-silver">Premium</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded bg-purple-100 text-xs">VIP</div>
                    <span class="text-seatsync-silver">VIP</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded bg-green-100 text-xs">WC</div>
                    <span class="text-seatsync-silver">Wheelchair</span>
                </div>
            </div>

            <!-- Price Info -->
            @if (!empty($selectedSeats))
                <div class="bg-seatsync-surface border-seatsync-border mt-6 rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-semibold text-white">Selected Seats:
                                    {{ count(array_column($selectedSeats, 'seat_id')) }}</h4>
                                <button wire:click="clearSelectedSeats"
                                    class="cursor-pointer rounded-md bg-red-500/20 px-3 py-1 text-sm text-red-400 transition-colors duration-200 hover:bg-red-500/30">
                                    Clear All
                                </button>
                            </div>
                            <div class="text-seatsync-silver mt-1 text-sm">
                                @foreach ($selectedSeats as $selectedSeat)
                                    @php
                                        $seat = \App\Models\Seat::find($selectedSeat['seat_id']);
                                    @endphp
                                    <span>Row {{ $seat->row }}, Seat {{ $seat->number }} -
                                        ${{ number_format($this->getSeatPrice($seat), 2) }}</span><br>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-seatsync-gold">
                                <div class="text-seatsync-silver text-sm">Total</div>
                                <div class="text-2xl font-bold">{{ $this->getTotalPrice() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('screenings.show', ['movie' => $screening->movie->id]) }}"
                class="bg-seatsync-surface border-seatsync-border text-seatsync-gold hover:bg-seatsync-goldhover rounded-lg border px-6 py-3 font-semibold transition-colors">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Back to Showtimes
            </a>

            <button wire:click="proceedToPayment"
                @if (!empty($selectedSeats)) class="cursor-pointer bg-seatsync-gold text-black px-8 py-3 rounded-lg font-semibold uppercase tracking-wider hover:bg-seatsync-goldhover transition-colors"
                @else
                    disabled class="bg-seatsync-gold text-black px-8 py-3 rounded-lg font-semibold uppercase tracking-wider transition-colors disabled:opacity-50 disabled:cursor-not-allowed" @endif>
                @if (!empty($selectedSeats))
                    Proceed to Payment
                @else
                    Select Seats to Continue
                @endif
            </button>
        </div>
    </main>
</div>
