# Creator Plans vs Student Plans — Separation

This doc describes how **creator plans** and **student plans** are kept separate in the app.

---

## 1. Two concepts

| Concept | Table | Purpose |
|--------|--------|--------|
| **Creator plans** | `plans` | Limits for **creators**: max quizzes, max batches, max students per batch, max AI generations/month, question bank access. Default plan for new creators. |
| **Student plans** | `student_plans` | Subscription tiers for **students**: Free, Premium, etc. Shown on student pricing page; students pay to get a plan (or activate free). |

---

## 2. Database

- **`plans`** (existing)  
  - Used only for creators.  
  - Fields: name, slug, description, price_label, max_quizzes, max_batches, max_students_per_batch, max_ai_generations_per_month, can_access_question_bank, is_default, sort_order, is_active.

- **`student_plans`** (new)  
  - Used only for students.  
  - Fields: name, slug, description, **duration** (`weekly` | `monthly` | `yearly`; default `monthly`), price_label, **price_paise** (nullable; null/0 = free), sort_order, is_active.

- **`users`**  
  - **`plan_id`** → `plans.id` (creator plan; only relevant for creators).  
  - **`student_plan_id`** → `student_plans.id` (student subscription; for students).

A user can have both: e.g. a creator with a creator plan and (if they also use the app as a student) a student plan.

---

## 3. Admin panel

- **Creator plans** (renamed from “Plans”)  
  - **System → Creator plans**  
  - CRUD: create/edit/delete creator plans.  
  - Assign from **Users → Edit user → Creator plan** dropdown.

- **Student plans**  
  - **System → Student plans**  
  - CRUD: create/edit/delete student plans (name, **duration**, price_label, **price_paise**, description, sort_order, is_active).  
  - Assign from **Users → Edit user → Student plan** dropdown.

---

## 4. Payments

- **Student plan purchase**  
  - Purpose: **`student_plan_purchase`**.  
  - `purpose_id` = `student_plans.id`.  
  - On success, `handlePostPayment` sets **`user.student_plan_id`** (not `plan_id`).

- **Legacy**  
  - Purpose **`plan_purchase`** still sets **`user.plan_id`** for backward compatibility (creator plan).

- **Initiate validation**  
  - When `purpose === 'student_plan_purchase'`:  
    - `purpose_id` must be an active **StudentPlan**.  
    - Amount must match that plan’s **price_paise** (in rupees).  
    - Free plans (price_paise null/0) must not use payment; use an “Activate” flow instead.

---

## 5. Models

- **Plan**  
  - Unchanged. Used for creator limits; `Plan::defaultPlan()` for new creators.

- **StudentPlan**  
  - New model.  
  - Constants: `DURATIONS` = `['weekly', 'monthly', 'yearly']`.  
  - Helpers: `isFree()`, `priceInRupees()`, `durationLabel()`, `durationSuffix()`, scopes `active()`, `ordered()`.

- **User**  
  - **`plan()`** → Creator plan.  
  - **`activePlan()`** → Creator’s effective plan (or default).  
  - **`studentPlan()`** → Student plan.  
  - **`activeStudentPlan()`** → Current student subscription (nullable).

---

## 6. Student-facing flow (to implement)

- **Pricing page**  
  - List **StudentPlan::active()->ordered()**.  
  - Free: “Activate” (POST to set `user.student_plan_id`).  
  - Paid: “Get this plan” → POST `/payments/initiate` with `purpose: 'student_plan_purchase'`, `purpose_id: student_plan.id`, `amount: student_plan.price_paise / 100`.

- **Success page**  
  - Already supports `student_plan_purchase` and shows “Your plan has been activated.”

---

## 7. Summary

| Where | Creator plan | Student plan |
|-------|--------------|--------------|
| Table | `plans` | `student_plans` |
| User field | `plan_id` | `student_plan_id` |
| Admin menu | Creator plans | Student plans |
| User edit | Creator plan dropdown | Student plan dropdown |
| Payment purpose | `plan_purchase` (legacy) | `student_plan_purchase` |
| After payment | `user.plan_id` | `user.student_plan_id` |

This keeps creator limits and student subscriptions separate and clear.
