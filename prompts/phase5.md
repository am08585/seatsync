# Phase 5 — Admin Reporting, Analytics & Management Dashboard  
**Project: SeatSync**  
**Stack: Laravel 12, Filament Admin, Livewire, Postgresql, Redis, Laravel Reverb/Echo**

---

## 🎯 Overview
Phase 5 introduces the **Admin Panel** built with **Filament**, enabling administrators to view system-wide analytics, manage movie/showtime data, monitor reservations, track capacity, and view revenue reporting.

This phase focuses on **insight**, **visibility**, **control**, and **operational monitoring**.

---

# 📌 INSTRUCTIONS FOR AI AGENT
- Implement everything using **Laravel 12** and **Filament** with best practices.  
- Follow all data rules established in previous phases.  
- Ask **no questions**, implement as instructed.  
- Produce complete code, migrations, resource definitions, widgets, charts, and reports.

---

# 🚀 Phase 5 Objectives

## 1. Create Admin Dashboard (Filament)
Implement a **custom Filament Admin Dashboard** that shows:

### A. High-Level KPI Cards
- **Total Movies**  
- **Total Screenings**  
- **Total Reservations**  
- **Total Revenue** (from confirmed reservations only)  
- **Today’s Reservations**  

### B. Charts & Analytics
Charts (Filament Widgets):

1. **Reservations Over Time**
   - Line chart  
   - X-axis: dates  
   - Y-axis: number of reservations  

2. **Revenue Over Time**
   - Line chart  
   - Sum of seat prices  

3. **Seat Occupancy by Screening**
   - Bar chart  
   - Screening vs. number of seats filled  

4. **Most Popular Movies**
   - Pie chart  
   - Percentage based on reservation count  

### C. Real-Time Summary Panels
Use Reverb broadcasting to update dashboards live when:
- reservations are created  
- reservations are cancelled  
- seats are released  

Widget auto-refresh via broadcast triggers.

---

# 2. Add Filament Resources for Admin CRUD

## Required Filament Resources:
1. **MovieResource**
2. **ScreeningResource**
3. **SeatLayoutResource** (read-only or edit-in-admin)
4. **ReservationResource**
5. **UserResource**
6. **GenreResource**

### MovieResource
- title, description, poster, genre
- screening relation manager

### ScreeningResource
- date/time  
- hall  
- related movie  
- capacity summary  
- seat layout read-only preview  
- reservation count

### ReservationResource
- reservation status  
- seats reserved  
- price  
- user  
- screening  
- cancellation logs  

### UserResource
- promote/demote to admin  
- list of user reservations  

### GenreResource
- genre name  
- relationship to movies  

---

# 3. Reporting Modules

Create a dedicated **Reporting Section** in admin sidebar:

Reports:
- Revenue Report
- Reservation Report
- Occupancy Report
- Cancellation Report


### A. Revenue Report
Filters:
- by date range  
- by movie  
- by screening  

Outputs:
- total revenue  
- reservation count  
- avg. revenue per reservation  
- downloadable CSV  

### B. Reservation Report
Filters:
- pending / confirmed / cancelled  
- date range  
- movie  
- user  

Outputs:
- seating map heat overview (seats most often reserved)  

### C. Occupancy Report
Calculates:
- total seats available per screening  
- total seats reserved  
- occupancy percent  

### D. Cancellation Report
Uses `reservation_logs` table:
- cancellation timestamps  
- user info  
- screening info  
- time before screening when cancelled  

---

# 4. Admin Tools for Operational Control

## A. Screening Dashboard Tools
Inside **ScreeningResource → View Page**, include:

### 1. Real-time seat chart
- read-only seat map  
- color-coded:
  - green: available  
  - gray: held  
  - red: reserved  

### 2. Force-release seats
Admin action:
- select seat(s)
- force-release from hold if stuck  
- triggers SeatReleased event

### 3. Force-cancel reservation
Admin action to cancel a reservation on behalf of user.

### 4. Export Seating Data (CSV)

---

# 5. Additional Service Layer Features

## A. ReportingService
Implement:

- revenue calculations  
- occupancy calculations  
- cancellation statistics  
- export to CSV  

## B. AdminBroadcastService
Handles:
- sending dashboard updates  
- notifying admins of abnormal activity  
  (e.g., high cancellation rate)

---

# 6. Additional Database Updates

### A. Add `price` column to `reservations` (if not stored already)
To prevent retroactive recalculation changes.

### B. Add `meta` JSON to screenings
For custom admin analytics (optional but required).

---

# 7. Filament Custom Widgets

Create:

### A. KPI Widgets  
- TotalReservations  
- TodayReservations  
- TotalRevenue  
- SystemCapacity  

### B. Chart Widgets  
- ReservationsChart  
- RevenueChart  
- OccupancyChart  
- PopularMoviesChart  

---

# 8. Testing Requirements

### A. Feature Tests
- Admin can view dashboards  
- KPIs calculate correctly  
- Filters work for all reports  
- CSV exports work  
- Only admins can access admin panel  
- Real-time widgets update when events fire  

### B. Unit Tests
- Revenue calculations  
- Occupancy calculations  
- Popular movies logic  
- Cancellation report logic  

---

# 9. Deliverables

AI must output:

1. All Filament resources  
2. All widgets (cards & charts)  
3. Admin dashboard  
4. All report pages  
5. ReportingService  
6. AdminBroadcastService  
7. Routes updates  
8. Tests (feature + unit)  
9. CSV exports  
10. Documentation  
11. Final summary and A Summary of the changes made to the codebase in this phase written in PHASE_5_SUMMARY.md file

---

# 📘 Final Summary (AI must append at the end of its output)

Final Summary – Phase 5

Phase 5 implements the administrative side of SeatSync using Filament Admin.
Admins gain full visibility into system performance, including real-time KPIs,
revenue and reservation analytics, seat occupancy, and cancellation reporting.
Comprehensive CRUD management enables control over movies, screenings, users,
and reservations. Filament widgets provide actionable insights, while CSV
exports allow external analysis. This phase completes the core admin pillar
of SeatSync and sets the stage for Phase 6: Deployment, Optimization, and
DevOps tooling (optional).

---

# END OF PHASE 5 PROMPT