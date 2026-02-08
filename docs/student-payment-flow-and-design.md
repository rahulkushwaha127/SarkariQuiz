# Student Flow: Payment Integration & Design Plan

Focus: **Students (and guests)** — how they see plans, pay, and use the app.

---

## 1. Current State (What You Have)

| Area | Status |
|------|--------|
| **Backend** | `PaymentController`: initiate, Razorpay verify, PhonePe callback, success/failed. Purpose `plan_purchase` + `purpose_id` → sets `user.plan_id`. |
| **Plans** | **Creator plans** (`plans` table) for creators; **Student plans** (`student_plans` table) for students. Student plans have `price_paise`; payment purpose `student_plan_purchase` sets `user.student_plan_id`. |
| **User** | `User` has `plan_id` (creator) and `student_plan_id` (student subscription). |
| **Student UI** | Phone-frame layout (max 420px), dark theme (slate-950, indigo). Sidebar: Home, Exams, Practice, PYQ, Revision, Clubs, Contests, Profile, etc. **No Plans / Pricing / Upgrade** page. |
| **Payment UI** | Success/failed views use **light** theme (white card); rest of student app is dark. |

---

## 2. Payment Integration Flow (Recommended)

### 2.1 Data: Student plan price

- **Student plans** (`student_plans` table) already have `price_paise` (nullable; null/0 = free). See **Creator vs Student plans** in `docs/creator-vs-student-plans-separation.md`.
- **Admin → Student plans:** Set price label and price (paise). Free = 0 or blank.
- **Student flow** uses `StudentPlan::price_paise` to call `/payments/initiate` with `purpose: 'student_plan_purchase'`, `purpose_id: student_plan.id`.

### 2.2 Student payment journey (step-by-step)

1. **Entry points (where “pay” appears)**  
   - **Plans / Pricing page** (new): list active plans; “Get this plan” → checkout.  
   - **Sidebar:** “Plans” or “Upgrade” link.  
   - **Dashboard:** optional “Upgrade” or “Your plan” card.  
   - **Paywall (optional later):** when a student hits a limit (e.g. quizzes), show “Upgrade to continue” with CTA to plans/checkout.

2. **Plans page (student-facing)**  
   - Route: e.g. `/plans` (auth-only recommended).  
   - Controller: e.g. `Student\PlansController@index`.  
   - Data: **`StudentPlan::active()->ordered()->get()`** (student plans, not creator plans).  
   - Show per plan: name, description, `price_label`, and **“Get this plan”** (or “Current plan” if `auth()->user()->student_plan_id === $plan->id`).  
   - If plan is free (`price_paise` null/0), show **“Activate”** (no payment; set `user.student_plan_id`).

3. **Checkout (initiate payment)**  
   - Student clicks “Get this plan” for a **paid** student plan.  
   - Frontend: POST `/payments/initiate` with:
     - `amount`: `student_plan.price_paise / 100` (rupees)
     - `purpose`: **`student_plan_purchase`**
     - `purpose_id`: **`student_plan.id`**
     - `gateway`: optional (or use site default from settings)
   - Backend: already creates `Payment`, creates gateway order, returns either:
     - **Razorpay:** `gateway_data` for inline checkout (open Razorpay checkout.js).
     - **PhonePe:** `redirect_url` → redirect user to PhonePe.

4. **After payment**  
   - **Razorpay:** JS calls `/payments/verify/razorpay` with payment_id + Razorpay response → then redirect to `/payments/{payment}/success`.  
   - **PhonePe:** User returns to `/payments/phonepe/callback/{payment}` → verify → redirect to success or failed.  
   - **Success page:** Already handles `plan_purchase` in `handlePostPayment` (sets `user.plan_id`). Show “Plan activated” and link to Home/Dashboard.

5. **Free plan**  
   - “Activate” button: simple POST (e.g. `Student\PlansController@activateFreePlan`) that sets `user.plan_id` and redirects to dashboard with success message. No payment call.

### 2.3 Security & validation

- **Auth:** All payment routes already under `auth` middleware; keep it.  
- **Plans page:** Ensure only `is_active` plans are shown; only show “Get this plan” for plans with `price_paise > 0` (paid) or “Activate” for free.  
- **Initiate:** Validate `purpose_id` is a valid, active plan and `amount` matches `Plan::find(purpose_id)->price_paise / 100`. Reject mismatch.  
- **Success/Failed:** Already restrict to `payment.user_id === Auth::id()`.

### 2.4 Suggested implementation order

1. **Migration:** Add `price_paise` to `plans`; update Admin plan form to set it.  
2. **Student PlansController + view:** List active plans; “Get this plan” / “Activate” (free).  
3. **Student checkout UI:** On “Get this plan”, call `/payments/initiate`; for Razorpay embed checkout.js and on success call verify then redirect; for PhonePe use `redirect_url`.  
4. **Free plan activation:** Endpoint to set `user.plan_id` for free plan, no payment.  
5. **PaymentController:** Validate amount vs plan’s `price_paise` in `initiate`.  
6. **Success/failed pages:** Restyle to match student (dark) theme.  
7. **Sidebar + dashboard:** Add “Plans” / “Upgrade” and optional “Your plan” card.

---

## 3. Design Suggestions (Student / Guest Side)

### 3.1 Keep existing student layout

- **Layout:** Keep the current phone-frame, single-column, max-width ~420px.  
- **Theme:** Dark (slate-950, slate-900, white/10 borders).  
- **Accents:** Indigo for primary CTAs; keep existing streak/XP and card styles.

### 3.2 Plans / Pricing page

- **List as cards:** One card per plan (same border/bg as dashboard cards: `border border-white/10 bg-white/5`).  
- **Current plan:** Badge or “Current plan” on the card; disable “Get this plan” or show “Current plan” text.  
- **Free plan:** “Activate” button (secondary style: `bg-white/10`).  
- **Paid plan:** “Get this plan” primary button (`bg-indigo-500`).  
- **Copy:** Short benefit list (e.g. “Unlimited quizzes”, “PYQ access”) from plan attributes; keep `price_label` prominent.

### 3.3 Checkout (modal or page)

- **Option A – Same page:** On “Get this plan”, show a small inline section or modal: “Pay ₹X with Razorpay/PhonePe” and gateway-specific UI (Razorpay script / PhonePe redirect).  
- **Option B – Dedicated checkout page:** `/plans/{plan}/checkout` with amount, plan name, and gateway choice; then initiate and show Razorpay/redirect.  
- Prefer **same layout** (phone frame + student layout) so navigation and sidebar stay consistent.

### 3.4 Success / Failed pages (align with student theme)

- **Success:** Use `layouts.student`; replace white card with dark card (`bg-slate-900/80 border-white/10`), green accent for success icon and “Plan activated”. Same CTA: “Go to Home”.  
- **Failed:** Same layout; red accent; “Try again” → back to plans or checkout.

### 3.5 Sidebar & dashboard

- **Sidebar:** Add “Plans” or “Upgrade” (icon: e.g. sparkles or credit-card) linking to `/plans`. For guests, same link; they’ll hit auth modal if you make plans auth-only.  
- **Dashboard:** Optional “Your plan” card (e.g. “Free” or plan name + “Upgrade” if you want to nudge). Don’t overcrowd; one compact row is enough.

### 3.6 Guest vs student

- **Guests:** Can browse; protected actions open auth modal.  
- **Plans page:** Either show to everyone (guests get “Login to get this plan”) or require login (redirect/modal). Recommended: **require auth** for `/plans` so only logged-in users see “Get this plan” and checkout.

---

## 4. Quick Checklist

- [x] **Creator vs Student plans separated:** `plans` (creator), `student_plans` (student); `student_plan_id` on users. See `docs/creator-vs-student-plans-separation.md`.  
- [x] Student plans have `price_paise`; PaymentController validates `student_plan_purchase` and sets `user.student_plan_id`.  
- [ ] Student `PlansController` + `/plans` view (list **StudentPlan**, current plan, Get/Activate).  
- [ ] Free student plan activation endpoint (no payment).  
- [ ] Checkout: call initiate with `purpose: student_plan_purchase`; Razorpay + PhonePe.  
- [ ] Success/failed views: dark theme, same student layout.  
- [ ] Sidebar: “Plans” link.  
- [ ] Optional: dashboard “Your plan” card and paywalls later.

This keeps the student flow consistent, secure, and ready to extend (e.g. paywalls, more gateways) later.
