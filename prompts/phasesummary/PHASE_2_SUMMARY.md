# Phase 2 implemented (Redis + DB audit + private broadcasting + tests)

## What I added / changed

### Redis hold service

- app/Services/SeatHoldService.php
- Uses atomic SET key value EX 600 NX (TTL = 10 minutes) with key format:
  hold:screening:{screening_id}:seat:{seat_id}
- Stores hold payload as JSON (user id + expiry + hold token)
- Writes an audit row into seat_holds and cleans stale DB rows when Redis key is missing.

### Controller + auth-protected web endpoints

- app/Http/Controllers/SeatHoldController.php
- Routes added to routes/web.php behind auth middleware:
  POST /screenings/{screening}/seats/{seat}/hold (seat-holds.hold)
  DELETE /screenings/{screening}/seats/{seat}/hold (seat-holds.release)
- Returns:
  201 on hold success (includes hold_token + expires_at)
  409 if already held
  404 if releasing a seat that isn’t held
  403 if trying to release someone else’s hold

### Form Requests

- app/Http/Requests/HoldSeatRequest.php
- app/Http/Requests/ReleaseSeatRequest.php
- Both ensure user is authenticated + validate bound route params exist.

### Private broadcasting

- Events created:
  app/Events/SeatHeld.php
  app/Events/SeatReleased.php
- Channel added in routes/channels.php:
  screening.{screeningId} → allowed for authenticated users only
- Important security fix: hold_token is NOT broadcast (only returned in the HTTP response).

### Feature tests

- tests/Feature/SeatHoldTest.php
- Covers:
  successful hold
  conflict on double-hold
  release
  expiration behavior via missing Redis key → DB cleanup
- Redis is mocked via Redis::shouldReceive('command') so tests don’t require a real Redis server.

### Verification

- Ran vendor/bin/pint --dirty ✅
- Ran php artisan test ✅ (all tests passing)

### Status

- Phase 2 complete and passing tests.
