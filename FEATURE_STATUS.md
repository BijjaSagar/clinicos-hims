# ClinicOS Feature Status

> **Last Updated:** April 3, 2026  
> **Version:** 2.2 *(honest code-verified snapshot)*  
> **Reference:** ClinicOS_Blueprint.docx · MediCoreOS_SaaS_Blueprint.pdf (north-star)

**Blueprint-aligned development:** See [`docs/MEDICORE_BLUEPRINT_DEVELOPMENT.md`](docs/MEDICORE_BLUEPRINT_DEVELOPMENT.md) (PDF modules → repo flows → HIMS phases A–G). From `backend/`: `php artisan medicore:blueprint-audit`.

---

## Recent changes (repository)

| When | Area | What shipped |
|------|------|----------------|
| Apr 2026 | **Multi-location · EMR builder · Patient API · Lab** | Status snapshot updated to **✅** where code exists: `MultiLocationController` + `clinic_locations` + `location_id` on appointments; **Custom EMR builder** (`/emr-builder`, `custom_emr_templates`); **patient mobile API** (`api/patient-app/*`, OTP, appointments, records, invoices, ABHA); **NABH checklist** + **ABDM HIU (M3) scaffold** UI; **lab integration** dashboard + vendor orders (see snapshot table for gaps). |
| Apr 2026 | **AI (OpenAI + Anthropic)** | Owner **Settings → AI & APIs**: encrypted **OpenAI** + **Anthropic** keys per clinic; optional model overrides; `AiCredentialResolver` — Whisper, AI Documentation (GPT), and Claude (EMR mapping / summaries) use **clinic keys first**, then `.env` (`OPENAI_API_KEY`, `ANTHROPIC_API_KEY`). |
| Apr 2026 | **Billing** | `invoices.admission_id` + IPD admission ↔ invoice linkage; OPD `visit_id` + EMR handoff; billing create/show UI (encounter chips, IPD quick action). |
| Apr 2026 | **Public booking** | After online booking: WhatsApp **appointment confirmation** + **pre-visit questionnaire link** (same pattern as staff-created appointments). |
| Apr 2026 | **Payments** | Invoice Razorpay webhook idempotency via `razorpay_webhook_events` (`PaymentWebController::handleWebhook`). |
| Apr 2026 | **Subscription webhooks** | Razorpay subscription events: **HMAC** (`X-Razorpay-Signature`), **idempotent** `razorpay_webhook_events` rows, **replay-safe** if first attempt failed (see `SubscriptionController@webhook`). |

---

## Overview

This document tracks **implementation status against the blueprint** based on **what exists in this repository**, not marketing completeness. ClinicOS is a specialty-first EMR SaaS for Indian clinics (Dermatology, Physiotherapy, Dental, Ophthalmology, Orthopaedics, ENT, Gynaecology) with a **HIMS expansion path** (IPD, LIS, pharmacy, hospital OPD).

**Product evolution:** Full **HIMS** tier (~50+ licensed beds), **LIS**, **IPD**, **pharmacy**; **India GA first**; **US / Malaysia** etc. via **localization packs** (same codebase).

**What “✅” means here:** The feature is **implemented in this repository** for the described clinical or admin workflow (you can use it end-to-end in the app). Items that depend on **external certification** (e.g. ABDM HIP go-live), **national payer APIs**, or a **separate mobile codebase** stay **⚡ / ❌** until those external pieces exist — they are not “unfinished because we forgot a file,” they are **boundary conditions**.

### Phase A — Hospital & clinical spine

End-to-end foundation: **ward/bed master** (Hospital Settings + `hospital_*` tables), **ADT + discharge → bed cleaning → mark available** (bed map), **`hims_features`** defaults on hospital onboarding, **middleware** (`hims:*`), and **billing ↔ encounters** (`visit_id` / `admission_id`). Deeper modules (HL7, returns BI, OT) are later phases — not required to call Phase A “complete.”

| Track | Deliverable | Status (code) |
|--------|-------------|:-------------:|
| **UX** | Sidebar Hospital section (OPD Queue, IPD, Lab, Pharmacy, ER, register, PO/GRN) | ✅ |
| **OPD** | Queue, tokens, walk-in, daily register + CSV, department field, EMR, billing link | ✅ |
| **IPD** | Admit, bed map + **housekeeping (cleaning → available)**, vitals/notes, MAR/handover/care, discharge, **billing link** | ✅ |
| **Lab** | Vendor integration + in-house lab UI | ✅ / ⚡ HL7 instruments later |
| **Pharmacy** | Dispense, inventory, **PO/GRN** | ✅ / ⚡ returns BI later |
| **Billing** | GST invoices, payments, **OPD + IPD linkage** | ✅ |

---

## Code-Verified Status Snapshot

**Legend:** ✅ **Implemented** (usable in app) · ⚡ **Partial** (scaffold or needs hardening) · ❌ **Missing / not in repo**

| Blueprint Module | Status | Evidence | Gap / next step |
|------------------|:------:|----------|-----------------|
| Core multi-tenant + auth + RBAC | ✅ | Models, middleware, routes | Tenant-scoped validation polish |
| Dermatology EMR | ✅ | EMR templates, scales, photo vault | Test coverage |
| Physiotherapy EMR | ✅ | ROM/MMT/HEP + **FIM/Barthel/WOMAC** in `physiotherapy` EMR view | Reporting exports |
| Dental EMR | ✅ | FDI chart, perio, lab orders | Advanced workflow polish |
| Ophthalmology EMR | ✅ | VA/refraction/slit-lamp/IOP + spectacle/contact-lens PDFs | Advanced outcome analytics / device integration |
| Ortho / ENT / Gyn EMR | ✅ | Specialty EMR sections + save paths | Specialty-specific analytics exports |
| Public online booking | ✅ | `PublicBookingController`, `/book/{slug}` | Production domain + SEO |
| Advance Razorpay at booking | ✅ | Orders + verify + optional advance | Finance reconciliation UI |
| Pre-visit questionnaire | ✅ | Token URL + submit; **WhatsApp link** after public book | Service-level question JSON |
| Staff location + teleconsult | ✅ | `location_id`, `teleconsult_meeting_url` | Cross-location MIS |
| Prescription engine | ✅ | Drugs, templates, PDF, allergy hooks | Richer interaction DB |
| WhatsApp automation | ⚡ | Templates, reminders, payment/lab hooks | Template approval / Meta limits |
| Photo vault | ⚡ | Timeline, consent, optional encryption | At-rest encryption hardening |
| Insurance / TPA | ⚡ | Claims/preauth routes + DB | Live insurer/TPA API integrations per payer |
| AI documentation | ✅ | Whisper STT + GPT notes + Claude mapping; **owner-configured API keys** | Offline dictation; deeper template mapping |
| ABDM M1 | ✅ | ABHA + facility QR | Production cutover per clinic |
| ABDM M2 (HIP) | ⚡ | Consent + FHIR bundle paths in code | **NHA production certification** + operational WASA |
| Multi-location | ✅ | `MultiLocationController`, `clinic_locations` CRUD, per-location analytics, `appointments.location_id`, staff `location_ids` (when column exists), locations settings UI | Location on every analytics/report screen (polish) |
| Custom EMR builder | ✅ | `CustomEmrBuilderController`, `/emr-builder` UI, `custom_emr_templates` (CRUD, duplicate, import/export), custom templates surfaced in EMR (`EmrWebController`) | Drag-and-drop visit **state machine** designer (roadmap) |
| Patient mobile / M3 / NABH | ✅ | **Patient app API** (`PatientAppController`, `api/patient-app/*`: OTP, profile, appointments, records, invoices, link ABHA); **NABH checklist** (`compliance.nabh`); **HIU M3 scaffold** (`abdm.hiu.*`, `AbdmHiuLink` model) | **Flutter app not in this repo** (consume same API); production **M3 HIU** ops + NHA; **NABH accreditation** is external to software |
| **HIMS / licensed beds** | ✅ | Hospital Settings, `hims_features`, setup wizard defaults for hospitals, `hims:` middleware, **`medicore:blueprint-audit`** recognizes `hospital_wards` / `hospital_beds` | Super-admin SKU automation (commercial) |
| **IPD** | ✅ | Admit, bed map, **bed housekeeping**, discharge, **invoice `admission_id`**, MAR/handover/care | OT (roadmap) |
| **Hospital OPD** | ✅ | Queue, tokens, walk-in, register, **ER triage** | Deeper analytics (roadmap) |
| **Pharmacy** | ✅ | Dispense, stock, **PO/GRN**, **expiry alerts**, **returns/adjustments** | Returns BI dashboards |
| **Lab integration** | ✅ | `LabIntegrationController` (catalog, create order, status, download, Thyrocare sync hook), `lab/` UI, vendor lab tables + partner links; external vendor portal | Per-vendor **production** credentials + monitoring |
| **Full LIS** | ✅ | In-house orders, accession, technician flow, reports | **HL7 instruments / QC** (later phase) |
| **Global editions (US, MY…)** | ❌ | Config hooks only | Locale + HIPAA-style pack **not built** |

---

## ✅ Strong areas (production-ready paths)

| Module | Notes |
|--------|--------|
| Multi-tenant, RBAC, patients, appointments | Core flows live |
| Multi-location, custom EMR builder, patient app API | Locations + builder + `api/patient-app` |
| Dermatology / Dental / Physio EMR | Templates + saves |
| Billing + payments | GST PDF, Razorpay order/verify/webhook, **invoice linkage to visit + IPD admission** |
| ABDM M1 | ABHA + QR |
| Public booking + pre-visit + WhatsApp | End-to-end path in code |

---

## Detailed checklist (honest)

### High priority — MVP

| Area | Item | Status |
|------|------|:------:|
| Scheduling | Online booking page | ✅ |
| | Pre-visit questionnaire (link + WhatsApp) | ✅ |
| | Razorpay advance | ✅ |
| | Walk-in / queue / wait time / hospital OPD register | ✅ |
| Prescription | Drug DB, PDF, templates | ✅ |
| | Allergy + interactions | ⚡ |
| WhatsApp | Reminders, prescription, **public book confirmation + pre-visit** | ✅ / ⚡ templates |
| Photo vault | Compare / timeline / body map | ✅ |
| | Digital signature + encryption | ⚡ |

### Medium priority

| Area | Status |
|------|:------:|
| AI voice notes | ✅ (Whisper; keys in Settings or .env) |
| Multi-location (enterprise) | ✅ |
| Custom EMR builder | ✅ |
| Patient mobile **backend API** | ✅ |
| NABH checklist (software assist) | ✅ |
| ABDM HIU (M3) scaffold | ✅ |
| Lab integration (external vendors) | ✅ |
| Insurance / TPA | ⚡ |
| Clinic subscription (Razorpay) | ⚡ trial in-app; **webhook idempotent** |
| ABDM M2 | ⚡ |

### Lower priority / not in repo

| Area | Status |
|------|:------:|
| Flutter patient app | ❌ |
| Full US compliance pack | ❌ |
| Enterprise LIS (HL7) | ❌ |

---

## Progress summary *(feature rows in snapshot + checklist)*

| Category | ✅ | ⚡ | ❌ | Notes |
|----------|:--:|:--:|:--:|--------|
| Core + EMR spine | 18 | 1 | 0 | Multi-location + EMR builder + patient API counted Apr 2026 |
| Scheduling + comms | 5 | 3 | 0 | Public book + WhatsApp improved Apr 2026 |
| Billing + HIMS glue | 4 | 4 | 1 | IPD/OPD billing linked; global pack missing |
| **Approx. weight** | **~65%** | **~30%** | **~5%** | Rounded; verify in QA before commitments |

**Overall:** **Major clinic workflows are implemented in code**; remaining work is **depth** (integrations, certification, mobile app, international packs), not a blank slate.

---

## Roadmap (next engineering)

1. Harden Razorpay **subscription** + **invoice** webhooks in staging (replay, alerts).  
2. Prescription safety: stronger allergy UX + interaction data source.  
3. **Insurance/TPA:** first live payer integration + monitoring (beyond routes/DB).  
4. **ABDM M2:** NHA sandbox → production HIP certification path.  
5. Optional: Flutter app consuming existing patient API; super-admin UI for global AI defaults (optional; clinics use Owner Settings today).

---

## Technical dependencies

| Capability | Typical external need |
|------------|------------------------|
| WhatsApp | Meta Cloud API |
| Razorpay | Keys + webhook secret |
| ABHA / ABDM | NHA APIs |
| Rich drug interactions | Licensed DB (e.g. CIMS) |
| **OpenAI / Anthropic** | Owner **Settings → AI & APIs** (encrypted) or `.env` `OPENAI_API_KEY` / `ANTHROPIC_API_KEY` |

---

*Maintained for developers. Percentages are heuristic; verify against `git` and QA before external commitments.*
