# Phase 2 — Seat Holds (Redis + DB Audit)

## Goal

Implement the seat hold system using Redis (with TTL) and a SeatHold table in the database for auditing. This will ensure that users can temporarily hold seats for a limited time before completing their reservation.

This phase involves:

-   Creating Redis keys for seat holds
-   Creating a database audit table for seat holds
-   Implementing the logic for seat hold creation, expiry, and release
-   Broadcasting events when seats are held or released
-   Writing basic tests to ensure holds are properly handled

---

# SECTION 1 — Redis API Service for Holds

1. **Create Redis Service:**

    - Create a service class (`SeatHoldService`) to handle the interaction with Redis.
    - This service should have methods like:
        - `holdSeat($screeningId, $seatId, $userId)`
        - `releaseSeat($screeningId, $seatId)`
        - `getHold($screeningId, $seatId)`
        - `isSeatHeld($screeningId, $seatId)`

2. **Redis Key Format:**
   Use Redis keys to represent held seats:
   hold:screening:{screening_id}:seat:{seat_id}
   Set expiration time (TTL) of **10 minutes** to auto-release seats.

3. **Handle Seat Hold Expiry:**
   Redis will handle TTL for expiry, but ensure that on the server-side (e.g., upon reloading), we check Redis for expired holds and clean up stale data.

---

# SECTION 2 — Seat Hold Controller & Logic

1. **Create `SeatHoldController`:**

    - Method `holdSeat`:

        - Ensure seat is available (check Redis).
        - Generate a unique `hold_token` for the session.
        - Save hold data in Redis with TTL and in the database.
        - Broadcast the `SeatHeld` event.

    - Method `releaseSeat`:

        - Remove hold data from Redis and the `seat_holds` table.
        - Broadcast the `SeatReleased` event.

2. **Implement the Seat Hold Flow:**

    - User selects seats → system calls `holdSeat`.
    - If successful, seats are held in Redis + database.
    - If expired, ensure Redis cleanup and DB audit is consistent.

3. **Seat Hold Events:**

    - **SeatHeld**: Broadcast event when a seat is held.
    - **SeatReleased**: Broadcast event when a seat is released.
      Use Laravel's `broadcast` functionality.

---

# SECTION 3 — Tests

1. **Write Feature Tests:**

    - **Test for holding a seat:**

        - Ensure Redis holds the seat with TTL.
        - Verify a `seat_hold` record is created in the database.

    - **Test for double-holding a seat:**

        - If a seat is already held, the system should return an error (conflict).

    - **Test for releasing a seat:**

        - Ensure seat is removed from Redis and database upon release.

    - **Test for expiration:**

        - Ensure that after 10 minutes, expired holds are removed from Redis and database.

---

# SECTION 4 — Deliverables Required from AI Agent

1. Redis service class (`SeatHoldService`) with all necessary methods.
2. `seat_holds` migration file and model.
3. Seat hold controller methods for `holdSeat` and `releaseSeat`.
4. Event broadcasts (`SeatHeld`, `SeatReleased`).
5. Feature tests:

    - successful seat hold
    - conflict on double hold
    - release and expiration tests.

6. Sample routes and controllers demonstrating usage.
7. Code documentation and inline comments.
8. A final summary of all the deliverables.

---

# SECTION 5 — Acceptance Criteria

The implementation is accepted when:

-   Redis holds are created and expire after 10 minutes (TTL).
-   Database holds are correctly stored in the `seat_holds` table.
-   Events are broadcast when seats are held or released.
-   All tests pass.
