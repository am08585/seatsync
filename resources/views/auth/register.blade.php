<x-layout title="Register - SeatSync">
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
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                </div>
                <h1 class="font-heading text-seatsync-gold mb-2 text-3xl font-bold">Join SeatSync</h1>
                <p class="text-seatsync-silver">Start your cinema journey today</p>
            </div>

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
                            <h3 class="text-seatsync-red text-sm font-medium">Registration Error</h3>
                            <ul class="text-seatsync-silver mt-2 list-inside list-disc text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="name" class="text-seatsync-silver mb-2 block text-sm font-medium">Full
                            Name</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="text-seatsync-silver/50 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                autofocus
                                class="bg-seatsync-bg border-seatsync-border placeholder-seatsync-silver/50 focus:ring-seatsync-gold block w-full rounded-lg border py-3 pl-10 pr-3 text-white transition-all duration-200 focus:border-transparent focus:outline-none focus:ring-2"
                                placeholder="Enter your full name">
                        </div>
                        @error('name')
                            <p class="text-seatsync-red mt-2 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

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
                                class="bg-seatsync-bg border-seatsync-border placeholder-seatsync-silver/50 focus:ring-seatsync-gold block w-full rounded-lg border py-3 pl-10 pr-3 text-white transition-all duration-200 focus:border-transparent focus:outline-none focus:ring-2"
                                placeholder="Enter your email">
                        </div>
                        @error('email')
                            <p class="text-seatsync-red mt-2 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password"
                            class="text-seatsync-silver mb-2 block text-sm font-medium">Password</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="text-seatsync-silver/50 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="bg-seatsync-bg border-seatsync-border placeholder-seatsync-silver/50 focus:ring-seatsync-gold block w-full rounded-lg border py-3 pl-10 pr-3 text-white transition-all duration-200 focus:border-transparent focus:outline-none focus:ring-2"
                                placeholder="Create a password">
                        </div>
                        @error('password')
                            <p class="text-seatsync-red mt-2 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="text-seatsync-silver mb-2 block text-sm font-medium">Confirm Password</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="text-seatsync-silver/50 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="bg-seatsync-bg border-seatsync-border placeholder-seatsync-silver/50 focus:ring-seatsync-gold block w-full rounded-lg border py-3 pl-10 pr-3 text-white transition-all duration-200 focus:border-transparent focus:outline-none focus:ring-2"
                                placeholder="Confirm your password">
                        </div>
                        @error('password_confirmation')
                            <p class="text-seatsync-red mt-2 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                    class="bg-seatsync-gold text-seatsync-bg hover:bg-seatsync-gold/90 focus:ring-seatsync-gold focus:ring-offset-seatsync-surface flex w-full transform items-center justify-center rounded-lg px-6 py-3 font-semibold transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2">
                    <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Create Account
                </button>

                <div class="border-seatsync-border border-t pt-6 text-center">
                    <p class="text-seatsync-silver text-sm">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="text-seatsync-gold hover:text-seatsync-gold/80 font-medium transition-colors duration-200">Sign
                            In</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-layout>
