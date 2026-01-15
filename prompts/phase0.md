# Phase 0 — Project Initialization for SeatSync.

## Check for things below like a todo list. if things already happened check them and don't change them if they are good and working

## Goal

Check the fresh Laravel Full-Stack application for installation or configurations:

-   Postgresql
-   Redis
-   Laravel Fortify
-   Laravel Livewire
-   Flowbite components
-   Laravel Reverb
-   Laravel Echo client configuration

No business logic is to be implemented yet. Only prepare the environment and verify the stack boots properly.

## Step 1 — Check for Laravel Fortify and frontend implementation

-   Confirm that:
    -   Login
    -   Registration
    -   Password reset
    -   Email verification
        are scaffolded correctly.

Deliverable:

-   Commands executed
-   Any file modifications
-   Screenshot or confirmation description of working auth pages

---

## Step 2 - check for redis connection and configurations.

## Step 3 — Install & Configure Laravel Reverb

1. Install Laravel Reverb package for Laravel.
2. Publish Reverb config & migrations.
3. Set up.
4. Add necessary keys to `.env`.
5. Add supervisor config (optional) for later deployment.

Deliverable:

-   Installation commands
-   Updated `.env.example` showing:
    -   BROADCAST_DRIVER
    -   WEBSOCKETS_HOST
    -   WEBSOCKETS_PORT
-   Instructions for running Reverb

---

## Step 5 — Configure Laravel Echo (Client)

1. Install Laravel Echo.
2. Configure Echo in `resources/js/bootstrap.js`.
3. Check Echo to use Reverb server.
4. Confirm sample connection in browser console.

Deliverable:

-   Updated bootstrap.js
-   Confirmed ability to establish WS connection

---

## Step 6 — Update .env and .env.example

Add:
CANCELLATION_WINDOW_MINUTES=60
WEBSOCKETS_HOST=ws://localhost
WEBSOCKETS_PORT=6001
REDIS_CLIENT=phpredis
QUEUE_CONNECTION=redis

Deliverable:

-   Updated .env.example and .env content block

---

## Step 7 — Project Structure Confirmation

Explain and verify the initial folder structure:

-   `app/`
-   `resources/views`
-   `resources/views/livewire`
-   `resources/js`
-   `routes/*`
-   `config/*`

Deliverable:

-   Overview of folders and what purpose they will serve in later phases

---

## Step 8 — Final Verification

Perform the following checks:

-   Web app loads login/register correctly.
-   Redis connection works (`php artisan tinker → Redis::set(...)` test).
-   WebSockets server successfully starts and accepts a client connection.
-   Vite frontend assets compile without errors.

Deliverable:

-   A verification summary confirming the entire stack is operational.

---

## Acceptance Criteria

-   Auth flows work (login/register).
-   Redis service available.
-   WebSockets echo connection is functional.
-   Tailwind + Flowbite UI compiles and displays correctly.
-   No business logic has been implemented yet.

---
