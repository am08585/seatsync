<div class="space-y-6">
    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Reservation Confirmed</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Reservation #{{ $reservation->id }}
                </p>
            </div>
            <div>
                <x-logout-form />
            </div>
        </div>

        <div class="mt-4 grid gap-2 text-sm text-gray-700 dark:text-gray-200">
            <div>
                <span class="font-medium">Status:</span>
                {{ ucfirst($reservation->status) }}
            </div>
            <div>
                <span class="font-medium">Payment reference:</span>
                <span class="font-mono text-xs">{{ $reservation->payment_reference ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="font-medium">Confirmed at:</span>
                {{ optional($reservation->confirmed_at)->format('Y-m-d H:i') ?? 'N/A' }}
            </div>
            <div>
                <span class="font-medium">Total:</span>
                ${{ number_format($reservation->total_price / 100, 2) }}
            </div>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <h2 class="text-lg font-semibold">Screening</h2>

        <div class="mt-3 grid gap-2 text-sm text-gray-700 dark:text-gray-200">
            <div>
                <span class="font-medium">Movie:</span>
                {{ $reservation->screening?->movie?->title ?? 'N/A' }}
            </div>
            <div>
                <span class="font-medium">Theater:</span>
                {{ $reservation->screening?->theater?->name ?? 'N/A' }}
            </div>
            <div>
                <span class="font-medium">Start:</span>
                {{ $reservation->screening?->start_time?->format('Y-m-d H:i') ?? 'N/A' }}
            </div>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <h2 class="text-lg font-semibold">Seats</h2>

        <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-900/40 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Seat</th>
                        <th class="px-4 py-3 text-right">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($reservation->seats as $seat)
                        <tr>
                            <td class="px-4 py-3 font-medium">Row {{ $seat->row }} - {{ $seat->number }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format(($seat->pivot->price ?? 0) / 100, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
