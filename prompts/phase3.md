# Phase 3 — Reservation Confirmation + Mock Payment + Seat Locking

**Project: SeatSync**  
**Stack: Laravel 12, Livewire, Redis, Postgresql, Laravel Fortify, Flowbite, Reverb/Echo**

---

## 🎯 Overview

Phase 3 implements the full workflow that converts **seat holds → confirmed reservations**, including Redis validation, DB locking, mock payment simulation, and real-time broadcasting.

This phase completes the **core reservation logic** of the SeatSync system.

---

# ✅ Instructions for AI Agent

- Follow all instructions **exactly**.
- Use **Laravel 12**, Livewire, Redis, Postgresql.
- Use the conventions established in Phase 1 & Phase 2.
- Ask **no questions** — output code & explanations only.

---

# 🚀 Phase 3 Objectives

## 1. Implement Reservation Finalization Workflow

User flow:

1. Seats are held in **Redis** (from Phase 2).
2. User clicks **Proceed to Payment**.
3. Backend prepares a **temporary reservation session** using a `hold_token`.
4. User is redirected to a **mock payment page**.
5. Mock payment is simulated as:
    - **Success**
    - **Failure**
6. On **success**:
    - Validate hold exists in Redis.
    - Validate DB hold exists and not expired.
    - Validate seat availability.
    - Create a `reservations` record.
    - Create `reservation_seats` pivot entries.
    - Delete seat holds from Redis + DB.
    - Broadcast **SeatReserved** event.
7. On **failure**:
    - Release + delete hold.
    - Broadcast **SeatReleased** event.

---

# ⚙️ 2. Backend Logic Requirements

## A. Create `ReservationService@finalize($holdToken, $paymentStatus)`

Must perform:

- Validate hold ownership (user must match).
- Validate Redis hold exists.
- Validate DB hold exists & not expired.
- Validate all seats are available.
- Wrap in **DB transaction**.
- Insert into:
    - `reservations`
    - `reservation_seats`
- Delete all related holds from Redis + DB.
- Broadcast:
    - **SeatReserved** on success
    - **SeatReleased** on failure

Use **pessimistic locking (`FOR UPDATE`)** to prevent race conditions.

---

# 💳 3. Mock Payment Flow

### Routes required:

`GET /payment/mock/{hold_token}`  
→ Shows simple mock UI with seat details + price.

**Two POST actions:**

- `POST /payment/mock/{hold_token}/success`
- `POST /payment/mock/{hold_token}/fail`

### UI Requirements:

- Implement a Livewire component:
    - **PaymentMockPage**
    - Uses Flowbite components.
    - Displays:
        - Held seats
        - Prices
        - Total
    - Buttons:
        - “Simulate Success”
        - “Simulate Failure”

---

# 📡 4. Real-Time Broadcasting (Reverb + Echo)

### Events to implement:

- `SeatReserved`
- `SeatReleased` (reuse from Phase 2)

Broadcast when:

- Reservation confirmed → **SeatReserved**
- Payment failure → **SeatReleased**
- Manual release → **SeatReleased**

Frontend should update seat states in real-time.

---

# 📝 5. Validation Rules

AI must enforce the following:

- Reservation must have a valid `hold_token`.
- Hold must belong to the requesting user.
- Hold must not be expired (≤ 10 minutes).
- All held seats must belong to the same screening.
- A seat cannot be reserved twice.
- Double-submit reservation must be rejected (idempotent).

---

# 💻 6. Livewire Components to Implement

### Component 1: **PaymentMockPage**

- Accepts `hold_token`
- Displays seats & total price
- Has buttons for mock success/failure

### Component 2: **ReservationSummaryPage**

- Shows the final confirmed reservation
- Includes:
    - receipt info
    - seat numbers
    - screening info
    - total price

---

# 🧪 7. Testing Requirements

## A. Feature Tests

- Payment success creates reservation.
- Payment failure releases holds.
- Expired hold → reservation blocked.
- Wrong user hold_token → forbidden.
- Double reservation attempt fails.
- Correct broadcasting events are fired.

## B. Unit Tests

- `ReservationService@finalize()` logic
- Price calculation
- Locking & seat availability enforcement

---

# 📦 8. Deliverables

1. Service class (`ReservationService`)
2. Events
3. Livewire components
4. Controllers
5. Routes
6. Redis helpers
7. Tests (unit + feature)
8. Documentation / diagrams
9. A Summary of the changes made to the codebase in this phase written in PHASE_3_SUMMARY.md file

---

# 🧩 9. Final Summary (AI must append to its output)

Final Summary – Phase 3

Phase 3 implements the core pipeline transforming held seats into confirmed reservations.
By combining Redis temporary holds with Postgresql permanent records, we guarantee high
concurrency safety and prevent double booking. The mock payment flow simulates
real-world processing, while the ReservationService handles the confirmation process
using database transactions and pessimistic row locking. Real-time seat updates are
broadcast via Laravel Reverb/Echo. Phase 3 completes the reservation core and sets
the stage for Phase 4: User Reservation Management & Cancellation Logic.

---

# END OF PHASE 3 PROMPT
