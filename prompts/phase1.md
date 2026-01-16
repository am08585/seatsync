# Phase 1 — Database Schema & Migrations (SeatSync)

## Goal

Define and implement the complete relational database schema and Eloquent model layer for the SeatSync movie reservation system.

This phase includes:

-   All migrations
-   All models with relationships
-   Seeders for initial data
-   Basic factories (optional but preferred)
    No business logic should be added yet — only structure.

---

# SECTION 1 — Entities & Relationships (Authoritative List)

You must generate migrations + models for the following:

1. User

    - id
    - name
    - email
    - password
    - is_admin (boolean)
    - email_verified_at
    - timestamps

2. Genre

    - id
    - name
    - timestamps

3. Movie

    - id
    - title
    - description
    - runtime (int)
    - poster_path
    - timestamps
    - Relationships:
        - belongsToMany Genre
        - hasMany Screening

4. genre_movie (pivot)

    - genre_id
    - movie_id

5. Theater

    - id
    - name
    - total_seats (optional)
    - timestamps
    - Relationship:
        - hasMany Seats

6. Seat

    - id
    - theater_id (FK)
    - row (string)
    - number (integer)
    - price_modifier (integer, in cents)
    - seat_type (enum: standard, vip, premium, wheelchair)
    - timestamps
    - Unique constraint: (theater_id, row, number)

7. Screening

    - id
    - movie_id (FK)
    - theater_id (FK)
    - start_time (datetime)
    - end_time (datetime)
    - base_price (integer, cents)
    - timestamps
    - Relationships:
        - belongsTo Movie
        - belongsTo Theater
        - hasMany Reservation
        - hasMany SeatHold (audit)
    - Unique constraint: (theater_id, start_time)

8. SeatHold (audit table)

    - id
    - user_id
    - screening_id
    - seat_id
    - hold_token
    - expires_at (datetime)
    - created_at
    - No updates (audit)
    - Index: hold_token
    - Unique constraint: (screening_id, seat_id, user_id, hold_token)

9. Reservation

    - id
    - user_id
    - screening_id
    - total_price (integer, cents)
    - status (enum: pending, confirmed, cancelled)
    - timestamps

10. reservation_seat (pivot)

-   reservation_id
-   seat_id
-   price (integer, cents)
-   Unique constraint: (reservation_id, seat_id)

---

# SECTION 2 — Migration Requirements

## General rules:

-   Add cascade deletes where safe:
    -   When deleting movies, delete screenings.
    -   When deleting screenings, delete seat_holds + reservation_seat + reservations.
-   Add proper indexes for:
    -   movie_id
    -   theater_id
    -   screening_id
    -   user_id
    -   hold_token
-   Add softDeletes for:
    -   Movie
    -   Theater
    -   Screening
        (not for seats or reservations)

## Special constraints:

-   Screening:
    -   unique: (theater_id, start_time)
-   Seat:
    -   unique: (theater_id, row, number)
-   ReservationSeat:
    -   unique: (reservation_id, seat_id)
-   SeatHold:
    -   unique: (screening_id, seat_id, hold_token)

---

# SECTION 3 — Eloquent Models

For each model:

-   Add correct relationships.
-   Add casts:
    -   price fields → integer
    -   is_admin → boolean
    -   enums as strings
-   Add `$fillable` or `$guarded = []`.
-   Add helpful docblocks with property annotations.

---

# SECTION 4 — Seeders

Create the following seeders:

1. **AdminUserSeeder**

    - Create a user:
      name: "Admin User"
      email: "admin@seatsync.test"
      password: hashed "password"
      is_admin = true

2. **GenreSeeder**
   Seed common genres:

    - Action
    - Drama
    - Comedy
    - Horror
    - Sci-Fi
    - Romance

3. **MovieSeeder**

    - Create 3–5 sample movies with genres attached.
    - Use placeholder poster image paths.

4. **TheaterSeeder**

    - Create 1–2 theaters.

5. **SeatSeeder**

    - For each theater:
        - Create an 8×12 layout (example)
        - seat_type assigned as:
            - Front rows = standard
            - Middle rows = premium
            - Back rows = vip
        - price_modifier:
            - standard = 0
            - premium = +200
            - vip = +400

6. Connect all seeders in DatabaseSeeder.

---

# SECTION 5 — Factory Definitions

(Optional but recommended)
Create factories for:

-   Movie
-   Screening
-   Seat
-   Reservation
-   ReservationSeat

Factories should generate realistic test data.

---

# SECTION 6 — Deliverables Required from AI Agent

1. All migration files.
2. All model files with relationships
3. All seeders
4. All factories
5. Example database diagram (ASCII or textual)
6. A final summary explaining:
    - Entity relationships
    - Constraints
    - Indexing choices
    - Why the schema prevents overbooking

---

# SECTION 7 — Acceptance Criteria

The implementation is accepted when:

-   All migrations run successfully.
-   Database schema matches the spec.
-   Models correctly define all relationships.
-   Factories successfully generate valid data.
-   No business logic is implemented yet.
