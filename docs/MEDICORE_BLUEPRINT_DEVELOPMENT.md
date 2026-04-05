# MediCoreOS blueprint → ClinicOS development process

> **Purpose:** Align day-to-day engineering with **MediCoreOS_SaaS_Blueprint.pdf** (8 core modules, 40+ sub-features, 6 dev phases) and the repo’s **HIMS expansion spec** (`docs/HIMS_EXPANSION_PLAN.md`).  
> **Stack (blueprint):** Laravel 11 · MySQL 8 · Redis · AWS · Flutter (mobile) · WhatsApp · AI (Whisper + Claude-style flows).

---

## 1. How the PDF maps to this repository

| MediCoreOS PDF module | What it means in code | Primary docs / config |
|----------------------|------------------------|-------------------------|
| **OPD** | Appointments, hospital queue (`OpdController`), EMR visit, billing to `visit_id` | Routes `opd.*`, `emr.*` |
| **IPD** | Admissions, beds/wards, vitals/notes, discharge, billing to `admission_id` | `IpdController`, `ipd_*` tables, invoices |
| **Pharmacy** | Inventory, OP/IP dispense | `PharmacyController`, `hims_features.pharmacy_*` |
| **Lab** | External vendor integration + in-house LIS | `LabIntegrationController`, `LabController`, `laboratory.*` |
| **Billing & accounting** | GST invoices, payments, Razorpay, TPA hooks | `BillingWebController`, `PaymentWebController` |
| **HALO AI** | Voice + clinical assistance | AI documentation controllers, Whisper routes |
| **Patient app + WhatsApp** | Reminders, links, pre-visit | `WhatsAppService`, `PublicBookingController`, patient API scaffold |
| **Admin & reporting** | Super admin, clinic settings, dashboards | `admin.*`, subscription, analytics views |

**Authoritative phased build (engineering):** **`docs/HIMS_EXPANSION_PLAN.md`** sections 8–9 (Phases **A→G**: master data/beds → IPD ADT → hospital OPD/ER → nursing/MAR → pharmacy → LIS → billing/MIS). Feature flags live in **`backend/config/hims_expansion.php`** and `clinics.hims_features` JSON.

---

## 2. End-to-end flows (blueprint “single patient journey”)

1. **Acquire:** Public booking `/book/{slug}` → optional Razorpay advance → **WhatsApp** confirmation + **pre-visit** link.  
2. **Front desk:** Appointment / walk-in → **OPD queue** → **check-in** → create or open **visit**.  
3. **Clinical:** **EMR** (specialty templates) → orders (lab / pharmacy / procedures).  
4. **Diagnostics:** Lab order → vendor or **in-house LIS** → results back to chart.  
5. **Pharmacy:** Prescription → **dispense** → stock FIFO.  
6. **Billing:** **Invoice** lines; link **OPD** (`visit_id`) and **IPD** (`admission_id`) where applicable; UPI/Razorpay.  
7. **Inpatient:** **Admit** → bed → vitals/notes → **discharge** → final bill hook.  
8. **Compliance / growth:** ABDM (ABHA, HIP), TPA when enabled, analytics.

Implementations must keep **`clinic_id`** on all writes and respect **`hims_features`** + **`hims:` middleware** when a route is hospital-only.

---

## 3. Development process (how we work)

| Step | Action |
|------|--------|
| **1. Pick a phase** | Use HIMS **A→G**; don’t start Phase F (full LIS HL7) before integration path is revenue-grade unless explicitly prioritized. |
| **2. Flag + migrate** | Add/extend tables with migrations; toggle **`hims_features`** keys in config + super-admin when UI is ready. |
| **3. API / web** | Controllers under `App\Http\Controllers\Web` (and API routes if mobile); Form Request validation; policies for RBAC. |
| **4. Observability** | `Log::info` on success paths, `Log::warning` on guardrails, structured context (`clinic_id`, entity ids). |
| **5. QA** | Manual flow per module; webhook idempotency for Razorpay; sandbox for ABDM/lab partners. |

---

## 4. Where we are vs blueprint (rolling)

Run from `backend/`:

```bash
php artisan medicore:blueprint-audit
```

This prints which **core tables** exist and suggests the **next HIMS phase** to focus on. Update **`FEATURE_STATUS.md`** when a slice ships.

---

## 5. Suggested next implementation slices (ordered)

1. **Phase A (spine):** ✅ Ward/bed master (`hospital_*` or legacy `wards`/`beds`), `medicore:blueprint-audit` recognizes both; discharge → cleaning → **Mark available** on bed map; setup wizard turns on hospital `hims_features` keys.  
2. **Phase B depth:** IPD transfer + discharge checklist polish (spec §1.1–1.3).  
3. **Phase C–E depth:** OPD analytics, LIS accessioning, pharmacy returns BI as needed.  
4. **Phase G partial:** Charge master hooks from pharmacy/lab into **unified** billing lines (`billing_unified`).

---

## 6. References

- `MediCoreOS_SaaS_Blueprint.pdf` — product modules, phases, commercial context.  
- `docs/HIMS_EXPANSION_PLAN.md` — detailed requirements + build order.  
- `FEATURE_STATUS.md` — honest code status.  
- `backend/config/hims_expansion.php` — feature keys.

*RH Technology / ClinicOS — engineering alignment doc.*
