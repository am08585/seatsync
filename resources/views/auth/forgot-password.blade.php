<x-layout title="Forgot Password - SeatSync">
    <div class="bg-seatsync-surface border-seatsync-border relative overflow-hidden rounded-2xl border shadow-2xl">
        <!-- Cinema Screen Effect Background -->
        <div class="from-seatsync-gold/10 to-seatsync-blue/10 absolute inset-0 bg-gradient-to-br via-transparent"></div>

        <!-- Cinema Seats Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-8 gap-1 p-8">
                @for ($i = 0; $i < 64; $i++)
                    <div class="bg-seatsync-gold aspect-square rounded-full"></div>
                @endfor
            </div>
        </div>

        <div class="relative p-8">
            <!-- Brand Header -->
            <div class="mb-8 text-center">
                <div
                    class="bg-seatsync-gold/20 border-seatsync-gold mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full border-2">
                    <svg class="text-seatsync-gold h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
                    </svg>
                </div>
                <h1 class="font-heading text-seatsync-gold mb-2 text-3xl font-bold">Reset Password</h1>
                <p class="text-seatsync-silver">No worries, we'll send you reset instructions</p>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-500/50 bg-green-500/10 p-4">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-green-400">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="border-seatsync-red/50 bg-seatsync-red/10 mb-6 rounded-lg border p-4">
                    <div class="flex items-start">
                        <svg class="text-seatsync-red mr-3 mt-0.5 h-5 w-5 shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-seatsync-red text-sm font-medium">Error</h3>
                            <ul class="text-seatsync-silver mt-2 list-inside list-disc text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="text-seatsync-silver mb-2 block text-sm font-medium">Email
                        Address</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="text-seatsync-silver/50 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            autofocus
                            class="bg-seatsync-bg border-seatsync-border placeholder-seatsync-silver/50 focus:ring-seatsync-gold block w-full rounded-lg border py-3 pl-10 pr-3 text-white transition-all duration-200 focus:border-transparent focus:outline-none focus:ring-2"
                            placeholder="Enter your email">
                    </div>
                    @error('email')
                        <p class="text-seatsync-red mt-2 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="bg-seatsync-gold text-seatsync-bg hover:bg-seatsync-gold/90 focus:ring-seatsync-gold focus:ring-offset-seatsync-surface flex w-full transform items-center justify-center rounded-lg px-6 py-3 font-semibold transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2">
                    <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                    Send Reset Link
                </button>

                <div class="border-seatsync-border border-t pt-6 text-center">
                    <p class="text-seatsync-silver text-sm">
                        Remember your password?
                        <a href="{{ route('login') }}"
                            class="text-seatsync-gold hover:text-seatsync-gold/80 font-medium transition-colors duration-200">Back
                            to Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-layout>
