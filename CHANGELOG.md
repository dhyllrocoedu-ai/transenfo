# Changelog

## v3.2.0 — Unreleased

### Added
- **Enforcer impounding referral**: On the citation show page, if the violation is `is_impoundable` and no clamp exists yet, Enforcers/Admins see a "Refer for Impounding" button that creates a `ClampingRecord` (status `AwaitingPayment`) and redirects to the impounding page.

### Fixed
- **500 error on registration/login** (`POST /account-procedure`): Supabase HTTP calls now catch `ConnectionException` gracefully. `attempt()` throws `ValidationException` instead of returning `null` (which caused `Auth::login(null)` → TypeError 500). Registration falls back to local-only user creation if Supabase is unreachable.

## v3.1.0 — Impounding Management + Clamping Request Overhaul

### Added
- **Impounding module**: Full workflow (Awaiting Payment → Paid → Waiting Release → Released → Archived) with dedicated `ImpoundingController` and views (index with filters/inline modals, show with timeline)
- **VehicleRelease model**: Tracks vehicle releases with release number, notes, timestamps, linked to `ClampingRecord`
- **Comprehensive LTO violations**: 15 violation types seeded with `is_impoundable` flag and accurate penalty amounts
- **Clamping request management**: New `ClampingRequestController`, policy, views (index+show), approve/reject/assign/resolve actions
- **ClampingRequest `assigned_to` FK**: Migration adding the column for officer assignment
- **Map radius fix**: Zone tracking circles converted to GeoJSON polygon circles for proper zoom-responsive scaling

### Changed
- **ClampingStatus enum**: `Active` → `AwaitingPayment`; added `Paid`, `WaitingRelease`, `Released`
- **Clamping request form redesigned**: Option B layout — wider container (1100px), 2-column top row (Reporter Info + Location/Map), full-width Vehicle Info & Evidence cards, GPS pinning with map click fine-tune
- **Guest layout**: Added `@stack('styles')` (was missing, breaking child page CSS overrides)
- **Payment permissions**: `PaymentPolicy@create` restricted to Cashier; added `update` gate
- **Clamping show view**: Fixed broken link to vehicle releases (now points to impounding detail)
- **Navigation**: Added Impounding + Clamping Requests nav links

### Fixed
- **Map not rendering**: Added `@stack('scripts')` to `guest.blade.php` — Maplibre JS was never executed
- **Clamping form layout**: Container width constraint removed, replaced with responsive 1100px wrapper
- **Payment edit view**: Removed stale `citation.vehicle` references

## v3.0.0 — Initial Release

### Added
- Vehicle/Driver management with removal support
- UI redesign with responsive layouts
- Interactive zone maps with real-time tracking
- TomSelect-powered member selection
- TEM integration with VCMS
- Transportation enforcement management core
