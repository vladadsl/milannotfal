# Notfall Card App

The Notfall Card is a life-saving ID and digital profile system. Each user receives a physical card with a unique QR code. When scanned, it opens their personal emergency profile showing vital data. Sensitive details are protected by a 4-digit PIN.

---

## Features
- Unique QR code + PIN auto-generated on registration
- Emergency profile with:
  - Name, allergies, blood type
  - Emergency contacts (one-click call)
  - Conditions, medications, dosages (PIN protected)
- Users can update data via app using their account
- Secure storage with encryption
- Admin panel for user and card management
- Progressive Web App (PWA) support for mobile/offline use

---

## Tech Stack
- **Backend & Frontend:** Laravel (Monolith, SPA support via Blade or Inertia.js)
- **Database:** MySQL / PostgreSQL
- **QR Code Generator:** `simplesoftwareio/simple-qrcode`
- **Authentication:** Laravel Breeze / Jetstream
- **UI:** Tailwind CSS + Alpine.js / Vue.js (via Inertia)

---

