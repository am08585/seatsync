<div class="space-y-6">
    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Mock Payment</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Hold token: <span class="font-mono text-xs">{{ $hold_token }}</span>
                </p>
            </div>
            <div>
                <x-logout-form />
            </div>
        </div>

        @if ($isExpired)
            <div
                class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200">
                This hold has expired. You can no longer confirm a reservation with this hold token.
            </div>
        @else
            <div
                class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-200">
                Choose a simulated payment outcome below.
            </div>
        @endif
    </div>

    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <h2 class="text-lg font-semibold">Screening</h2>

        <div class="mt-3 grid gap-2 text-sm text-gray-700 dark:text-gray-200">
            <div>
                <span class="font-medium">Movie:</span>
                {{ $screening->movie?->title ?? 'N/A' }}
            </div>
            <div>
                <span class="font-medium">Theater:</span>
                {{ $screening->theater?->name ?? 'N/A' }}
            </div>
            <div>
                <span class="font-medium">Start:</span>
                {{ $screening->start_time?->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <h2 class="text-lg font-semibold">Held Seats</h2>

        <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-900/40 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Seat</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3 text-right">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($seats as $seat)
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="px-4 py-3 font-medium">
                                Row {{ $seat->row }} - {{ $seat->number }}
                            </td>
                            <td class="px-4 py-3">
                                {{ ucfirst($seat->seat_type) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                ${{ number_format($seatPriceCents($seat) / 100, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Total</span>
            <span class="text-lg font-semibold">${{ number_format($totalCents / 100, 2) }}</span>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <form method="POST" action="{{ route('payment.mock.success', ['hold_token' => $hold_token]) }}"
                class="w-full">
                @csrf
                <button type="submit" @disabled($isExpired)
                    class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                    Simulate Success
                </button>
            </form>

            <form method="POST" action="{{ route('payment.mock.fail', ['hold_token' => $hold_token]) }}"
                class="w-full">
                @csrf
                <button type="submit" @disabled($isExpired)
                    class="w-full rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-60">
                    Simulate Failure
                </button>
            </form>
        </div>
    </div>
</div>
