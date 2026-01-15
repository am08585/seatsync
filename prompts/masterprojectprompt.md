# Project Context

We are building "SeatSync", a movie reservation system using:

-   Laravel 12 (Full Stack + Livewire)
-   Laravel Fortify for backend auth scaffolding
-   Flowbite component library (UI)
-   Postgresql for Database
-   Redis (for seat holds)
-   Laravel Reverb (for real-time seat updates) + Laravel Echo
-   Filament for admin panel
-   Mock payment system with UI-based success/failure simulation

# Business Rules

-   Users must be authenticated to hold or reserve seats.
-   Holds last 10 minutes (TTL).
-   Hold + reservation concurrency must be safe (Redis + DB + transactions).
-   Reservation not cancellable within <= 60 minutes of showtime (configurable via .env).
-   Reservation price = screening.base_price + seat.price_modifier.
-   Holds stored in Redis + logged in seat_holds table.
-   Reservations stored in DB, finalized only after payment.

# Development Principles

-   Never assume missing details — ask clarifying questions when ambiguous.
-   Code must follow Laravel 12 best practices.
-   Use clean architecture & readable naming.
-   Generate database migrations, models, controllers, Livewire components, and tests.
-   Include docblocks, type hints, and comments.
-   Include feature & unit tests for each step.
-   Use FLowbite components in views - if can't find suitable flowbite component only then use custom component
