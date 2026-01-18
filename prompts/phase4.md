# Phase 4 — Reservation Management + Cancellation Rules + Real-Time Updates

**Project: SeatSync**  
**Stack: Laravel 12, Livewire, Postgresql, Redis, Reverb/Echo, Laravel Fortify, Flowbite**

---

## 🎯 Overview

Phase 4 introduces the **user-facing reservation dashboard**, the **cancellation workflow**, and **real-time updates** for seat availability when cancellations occur.

This phase completes the user reservation lifecycle.

---

# 📌 INSTRUCTIONS FOR AI AGENT

- Follow all steps exactly.
- Do not ask questions.
- Use Laravel 12, Livewire, and previously created conventions.
- Output all code, UI, services, and tests exactly as requested.

---

# 🚀 Phase 4 Objectives

## 1. Implement User Reservation Dashboard

Create a new page:

`/reservations`

Shows all reservations for the authenticated user:

- Upcoming reservations (sorted soonest first)
- Past reservations
- Cancel buttons (only upcoming & eligible)
- Status badges:
    - confirmed
    - cancelled
    - expired

Fields displayed:

- Movie title
- Screening date/time
- Seats (list)
- Total price
- Reservation number
- Status

---

# 2. Implement Reservation Cancellation Logic

User can cancel **only if ALL conditions are true**:

1. Reservation belongs to the logged-in user
2. Reservation is **confirmed**
3. Screening starts in **more than 60 minutes**
4. Reservation is not already:
    - cancelled
    - expired

---

## 3. Cancellation Workflow Steps

### When user clicks **Cancel Reservation**:

1. Validate eligibility rules
2. Wrap whole operation in a **DB transaction**
3. Update reservation:
    - `status = "cancelled"`
    - `cancelled_at = now()`
4. Release seats:
    - Delete rows from `reservation_seats`
        - or mark them as free (depending on model)
    - Fire:
        - `SeatReleased` event (real-time seat map update)
        - `SeatCancellationNotice` event (UI notification to watchers)
5. Log cancellation in `reservation_logs` (new table)
6. Redirect user to summary page with success message

---

# 4. Database Changes

## A. Add `cancelled_at` column to `reservations` table

## B. Create `reservation_logs` table

Fields:

- id
- reservation_id
- user_id
- action (cancelled, expired, etc.)
- details (JSON)
- created_at

---

# 5. New Service Method

### Implement:

**ReservationService@cancel(Reservation $reservation)**

Responsibilities:

- Enforce all eligibility rules
- Acquire seat row locks (`FOR UPDATE`)
- Release seats (delete or mark free)
- Update reservation status
- Broadcast real-time events
- Log cancellation
- Return success response

---

# 6. Real-Time Broadcasting

Add new event:

### **SeatCancellationNotice**

Broadcast when a user cancels a reservation.

Payload:
screening_id
seat_ids
message: "Some seats have become available due to a cancellation."

This event is separate from SeatReleased to allow UI distinction.

### Broadcasting rules:

- **SeatReleased**
    - Updates seat map availability instantly
- **SeatCancellationNotice**
    - Displays a toast/alert to users watching the screening

---

# 7. Livewire Components

### A. ReservationList

- Shows all user reservations
- Includes “Cancel” button for eligible reservations

### B. ReservationCancelConfirm

- Modal for user confirmation
- Calls ReservationService@cancel

### C. ReservationCancelledPage

- Shows:
    - seats released
    - timestamp
    - refund info (mock, since payment is also mock)

---

# 8. Flowbite UI Requirements

Every UI must use:

- Flowbite modal for cancellation confirmation
- Flowbite alert component for success messages
- Flowbite badges for reservation statuses

---

# 9. Testing Requirements

## A. Feature Tests

- User can view their own reservations
- User cannot view others’ reservations
- Eligible cancellation succeeds
- Cancellation inside 60 minutes → fails
- Cancellation updates reservation status
- Cancellation releases seats
- SeatReleased & SeatCancellationNotice events fired

## B. Unit Tests

- ReservationService@cancel business rules
- Seat release logic
- Event firing correctness

---

# 10. Deliverables

AI must output:

1. Migrations
2. Models + relationships updates
3. ReservationService@cancel method
4. All Livewire components
5. Flowbite UI views
6. Events + broadcasting configuration
7. Routes
8. Feature + Unit tests
9. Documentation / diagrams
10. Final summary and A Summary of the changes made to the codebase in this phase written in PHASE_4_SUMMARY.md file

---

# 📘 Final Summary (AI must append at end of output)

Phase 4 introduces the full reservation lifecycle management system. Users can
view, manage, and cancel their reservations. Cancellations follow a strict set
of business rules, ensuring that seats are released only when valid. SeatSync
leverages real-time broadcasting to notify other users watching the seat map,
using SeatReleased for seat availability updates and SeatCancellationNotice
for user-facing alerts. This phase completes the user reservation management
workflow and prepares the system for Phase 5: Admin Reporting & Analytics.

---

# END OF PHASE 4 PROMPT
