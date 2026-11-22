# Puihaha Membership System: Execution Plan

**Deadline:** today (Mon 23 Nov 2026) · **Team:** 2 people, each with an AI assistant
**Stack:** Laravel + MySQL, Breeze (Blade), Tailwind, GSAP (via Vite), simulated payment gateway

Companion file: `AI_GUARDRAILS.md`. Put it in the repo root and paste it at the start of **every** AI session.

---

## 0. Operating loop

Every task, no exceptions:

1. **Cite** the rubric row it serves. If you can't name one, don't build it.
2. **Plan** with the AI first (10 lines max), then approve.
3. **Build** the smallest slice that satisfies the row.
4. **Verify** with a command and a manual click-through. Never accept "it should work".
5. **Tick** the rubric row in section 1 and **commit**.

Gates sit at the end of each phase. Don't start the next phase until the gate passes.

---

## 1. Rubric traceability matrix

Tick the box only when the proof has actually been run or shown.

| Criterion | Pts | "Done" means | Proof | Phase |
| --- | ---: | --- | --- | --- |
| Laravel setup & DB connection | 15 | App boots; `php artisan migrate:fresh --seed` runs clean from zero; every relationship resolves | Command output; in `tinker`: `$member->category`, `$member->level`, `$txn->payments`, `$txn->member` all return data | 1 |
| CRUD: Members | 20 | Create, view, edit, deactivate; validation (required, email format, unique email, unique membership no.); search by name, category, level | Click-through; submit invalid forms and see errors; search each filter | 3 |
| Category & level management | 15 | Full CRUD for both; every member has exactly one of each; deleting an in-use category or level is blocked with a friendly message | Try deleting an assigned category; try creating a member without a level | 2 |
| Transaction processing | 20 | Application, renewal, payment, status, history, reference no.; only Paid can be Approved | Demo checklist (section 10) | 4, 5 |
| Database design | 10 | ERD matches migrations; PK/FK, unique indexes, `restrictOnDelete`, no duplicated data except the documented `amount_due` snapshot | ERD image next to `migrate:fresh` output | 1 |
| Interface & usability | 10 | One layout, consistent components, usable at 375px width, flash messages, empty states, every page reachable from nav | Resize browser to 375px; navigate with no typed URLs | 6, 7 |
| Code quality | 5 | MVC, Form Requests, no business logic in Blade, no copy-pasted blocks | Skim controllers: each action is short | continuous |
| Documentation & presentation | 5 | README, ERD, screenshots of every page, demo script | Files exist in `/docs` | 8 |

**Weighting note:** transactions + members + categories/levels + DB = 65 points. GSAP and the gateway simulation don't appear in the rubric. They support *Interface* (10) and *Transaction processing* (20), so they are timeboxed and can be cut (section 9).

---

## 2. Scope decisions

**In scope**
- Everything in the spec, exactly as written.
- Simulated payment gateway (section 6).
- GSAP motion layer (section 7).
- Seeded demo data so nothing is empty during the demo.

**Out of scope (AI must refuse to add these)**
- Roles/permissions beyond a single admin
- Member self-service portal or member login
- Email/SMS notifications
- Real payment SDKs (Stripe, PayMongo, etc.)
- File uploads, avatars, REST API, queues, scheduled jobs
- Tests (only if you finish early)

**Schema addition for the gateway:** `payments` gets `status` (`succeeded`/`failed`), `gateway_reference` (unique), and `method`. Put these in the Phase 1 migration so you never edit the schema later.

---

## 2b. Design philosophies

Full rules are in `AI_GUARDRAILS.md` section 4b. Summary and how to check each:

| Principle | In this project | Rubric row | Quick check |
| --- | --- | --- | --- |
| **DRY** | Status/type/method enums; shared Form Request rules; Blade components for badge, field, flash, table, empty state; model scopes; one formatting helper | Code quality (5) | Search for a status string like `'Paid'` outside the enum; look for any Blade block pasted twice |
| **KISS** | Boring Laravel defaults; only `TransactionService` and `PaymentGateway` as abstractions; small single-purpose methods | Code quality (5), and it protects the timebox | Can a teammate explain each controller action in 30 seconds? |
| **WYSIWYG** | Screen = database = printout: confirmation matches what's saved, receipt matches the transaction page, totals come from the DB, blocked actions say why, forms keep input on error | Interface (10), Transaction processing (20) | Compare dashboard count, DB count, and receipt amounts; print preview vs. screen |

When they conflict: **rubric correctness > KISS > DRY > polish.** If extracting a component would take longer than copying it once, copy it once and extract on the second repeat.

---

## 3. Ownership (prevents merge conflicts)

| | Person A | Person B |
| --- | --- | --- |
| Phase 1 | together | together |
| Owns | Categories, Levels, Members, Dashboard | Transactions, Payments, Gateway, History, Receipt |
| Files | `Member*`, `Category*`, `Level*`, dashboard view | `Transaction*`, `Payment*`, `Gateway/`, receipt/history views |
| Shared (ask before editing) | `routes/web.php`, `layouts/app.blade.php`, nav, `tailwind.config.js` | same |

Branch per person (`feat/members`, `feat/transactions`). Merge to `main` at the merge gate, then again after Phase 6.

---

## 4. Phases

Timeboxes are hours from now. If a phase runs 30 minutes over, jump to section 9.

### Phase 0: Kickoff and creative decisions (0:00 to 0:45, together)

Goal: lock design tokens and the plan so nobody debates them later.

Tasks:
- One person creates the repo, the other starts the creative prompts in section 5.
- Pick **one** design direction, write it into `DESIGN.md`, and freeze it.

**Gate:** `DESIGN.md` exists with fonts, palette, and status-badge colors. Repo is on GitHub.

### Phase 1: Foundation (0:45 to 1:45, together)

Goal: rubric rows *Laravel setup* (15) and *Database design* (10).

Prompt:
```text
Read AI_GUARDRAILS.md first. Phase 1 of PLAN.md.
Task: scaffold the foundation for the Puihaha membership system.
1. Laravel + Breeze (Blade). MySQL .env.example filled in.
2. Migrations, models with all relationships, and factories for: members,
   membership_categories, membership_levels, membership_transactions, payments.
   Use the column list in PLAN.md and the payments additions (status,
   gateway_reference unique, method). restrictOnDelete on category/level FKs.
   Soft deletes on members. Unique: members.membership_number, members.email,
   transactions.reference_no, payments.gateway_reference.
3. Seeder: 1 admin user, 4 categories (Student, Regular, Senior Citizen,
   Corporate), 4 levels (Basic, Silver, Gold, Platinum) with fee,
   validity_months, benefits, and 12 demo members spread across them.
4. Membership number and transaction reference generators (model hook or
   small service). Formats: PUI-YYYY-000001, TXN-YYYYMMDD-0001.
Before coding, give me a 10-line plan and the rubric rows this serves.
After coding, run migrate:fresh --seed and show me the output, then show
tinker proof that each relationship resolves.
```

**Gate:** `migrate:fresh --seed` clean on both machines; tinker proof passes; ERD drafted on dbdiagram.io or draw.io and saved to `/docs/erd.png`. After this gate, the schema is frozen. Changes need both people's agreement and a re-run of `migrate:fresh`.

### Phase 2: Categories and levels (A: 1:45 to 3:00)

Rubric: *Category & level management* (15).

Prompt:
```text
Read AI_GUARDRAILS.md. Phase 2, Person A. Build full CRUD for membership
categories and membership levels with Route::resource, one Form Request
each, thin controllers, Blade views that extend the layout, and flash
messages. Deleting a category or level that has members or transactions
must be blocked with a friendly error (catch the restriction, do not let
the SQL error surface). Level form includes fee, validity_months, benefits.
Show a member count per category/level in the index tables.
Plan first, then build, then list which rubric checks pass.
```

**Gate:** try deleting an in-use category and level, and see the friendly error.

### Phase 3: Members (A: 3:00 to 4:45)

Rubric: *CRUD: Members* (20).

Prompt:
```text
Read AI_GUARDRAILS.md. Phase 3, Person A. Member CRUD: index (paginated,
search by name via query string, filter by category and level, using
when() clauses), create/edit via StoreMemberRequest/UpdateMemberRequest
(required fields, email format, unique email ignoring the current record on
update, valid category_id and level_id), show page, and deactivate/reactivate
(is_active toggle plus soft delete or status, never hard delete). The
membership number is generated, never typed. The show page has a placeholder
section for transaction history that Person B will fill. No business logic
in Blade.
```

**Gate:** submit every invalid case; search by name, category, level; deactivate and reactivate a member.

### Phase 4: Transaction domain (B: 1:45 to 4:00)

Rubric: *Transaction processing* (20).

Prompt:
```text
Read AI_GUARDRAILS.md. Phase 4, Person B. Transaction logic first, UI second.
Domain (in the model or a TransactionService, NOT in controllers or Blade):
- createApplication(Member, Level): snapshots amount_due from the level fee,
  generates reference_no, status Pending.
- createRenewal(Member): new transaction; starts_on is the current
  expires_on if still valid, otherwise today; expires_on = starts_on +
  validity_months.
- recordPayment(Transaction, amount, method): see Phase 5.
- approve(): allowed ONLY when status is Paid, otherwise throws a domain
  exception that the controller turns into a validation-style error.
- cancel(): allowed only from Pending.
- Expired: computed with a scope/accessor from expires_on, not a cron job.
Then UI: transactions index (filter by status), create application/renewal
forms, show page. Use Form Requests. Plan first, list rubric checks after.
```

**Gate:** in tinker, approving a Pending transaction fails; approving a Paid one works; renewal dates calculate correctly for both valid and lapsed memberships.

### Phase 5: Payment gateway simulation (B: 4:00 to 5:30)

Rubric: *Transaction processing* (20). Spec in section 6.

Prompt:
```text
Read AI_GUARDRAILS.md and PLAN.md section 6. Phase 5, Person B.
Build the simulated gateway exactly as specified: a PaymentGateway
interface, a SimulatedGateway implementation, a fake hosted checkout page,
and a callback route that records the Payment inside DB::transaction() and
updates the transaction status. Never store or ask for real card numbers.
Include the validation rules from the spec (valid transaction, not
cancelled, not already Paid, amount > 0 and <= remaining balance).
Show me the route list and a walkthrough of one success and one failure.
```

**Gate:** partial payment leaves status Pending; full payment flips to Paid; failed payment records a failed row and does not change status; the Approve button/action stays blocked until Paid.

### Merge gate (5:30 to 6:15, together)

1. Merge both branches into `main`. Resolve conflicts together.
2. `migrate:fresh --seed`, then run the demo checklist (section 10).
3. Fix integration bugs only. No new features.
4. Take a 30-minute break after this. Bugs at hour 9 on no rest cost more than the break.

### Phase 6: Dashboard, history, receipt (6:15 to 7:45)

Rubric: *Interface* (10), *Transaction processing* (history).

Prompt (A, dashboard):
```text
Read AI_GUARDRAILS.md. Dashboard with four stat cards: total members,
active members, pending transactions, total collected payments (sum of
succeeded payments only). Compute in the controller with efficient
queries, no N+1. Add data-count-to attributes on the numbers for the GSAP
count-up. Add a recent-transactions table below.
```

Prompt (B, history and receipt):
```text
Read AI_GUARDRAILS.md. Member transaction-history page (all transactions
with payments, status badges using the tokens in DESIGN.md) and a
printable receipt/membership record: plain Blade view with a @media print
stylesheet, reference number, member details, level, amounts, payments,
a Print button. Receipt available only for Paid/Approved transactions.
```

**Gate:** every page reachable from the nav; numbers on the dashboard match the database.

### Phase 7: GSAP and polish (7:45 to 9:00)

Spec in section 7. Also do: empty states, 375px check, consistent flash messages.

**Gate:** with `prefers-reduced-motion` on, everything is visible and static. With JS disabled, all content still appears.

### Phase 8: Documentation and demo (9:00 to 10:30)

Rubric: *Documentation* (5).

Prompt:
```text
Read AI_GUARDRAILS.md. Write README.md: project summary, stack, setup
(composer install, npm install, .env, migrate --seed, npm run build,
php artisan serve), admin credentials, feature list mapped to the rubric
criteria, business-rule list (status flow, approval rule, payment rules,
renewal rule, amount_due snapshot), the simulated gateway explanation, and
a screenshots section with placeholders for these pages: login, dashboard,
members list, member form, categories, levels, transactions, payment
checkout, history, receipt. Do not invent features that don't exist.
```

Then take the screenshots, drop them in `/docs`, finalize the ERD, and rehearse the demo twice.

**Buffer:** 1.5 hours. Don't plan to use it. It will get used.

---

## 5. Creative direction prompts

Do this in Phase 0, **once**, and lock the result. Ask your AI for options, pick one fast, and move on.

### 5a. Design direction

```text
I'm building the admin interface for the Puihaha E-Learning Platform
membership system (member records, membership levels Basic/Silver/Gold/
Platinum, payments, receipts). Users are administrators doing repetitive
data work. It's a student project with a one-day build and a rubric that
scores "clean, responsive, easy-to-use".
Give me 3 distinct visual directions. For each: a 2-sentence personality
description, why it fits an e-learning membership brand, and what it would
feel like for an admin who uses it for hours. Then recommend one and say
what you'd trade off by choosing it.
Constraints: Tailwind CSS, Laravel Blade, must stay legible and
professional, nothing that costs more than 30 minutes to implement.
```

### 5b. Typography

```text
For direction [chosen direction], recommend a font pairing (heading + body,
plus a mono option for reference numbers like TXN-20261123-0001). Free
fonts only, available through Fontsource or Google Fonts. Give: font names,
weights to load (keep it to 3-4 total), the type scale as Tailwind classes
for h1/h2/h3/body/small/table text, and the reasoning for the pairing.
Include the exact install steps for Vite and the Tailwind config change.
```

### 5c. Color palette and tokens

```text
Create a color palette for direction [chosen direction] as CSS variables
and a tailwind.config.js extension. Include: primary, primary-hover,
accent, background, surface, border, text, muted text, success, warning,
danger. Then define status badge styles for Pending, Paid, Approved,
Expired, Cancelled, plus the four membership levels Basic/Silver/Gold/
Platinum. Requirements: every text/background pair passes WCAG AA
contrast (state the ratios); badges must be distinguishable without color
alone (add an icon or label treatment); give hex values and the Tailwind
class names I should use. Do not exceed 6 core colors beyond the semantic ones.
```

### 5d. Record the decision

Save to `DESIGN.md` (template):

```markdown
# Design Decisions (LOCKED, do not add colors or fonts outside this file)
- Direction: 
- Heading font / body font / mono font: 
- Weights loaded: 
- Palette (hex + token name): 
- Status badge map (Pending / Paid / Approved / Expired / Cancelled): 
- Level badge map (Basic / Silver / Gold / Platinum): 
- Border radius / spacing rhythm: 
- Motion personality (speed, easing): 
```

### Fallback if you don't want to decide

Use this and move on: **Plus Jakarta Sans** (headings) + **Inter** (body) + **JetBrains Mono** (reference numbers). Indigo primary (`#4338CA`), amber accent (`#F59E0B`), slate neutrals (`#0F172A` text, `#F8FAFC` background, `#E2E8F0` borders). Status: Pending amber, Paid sky, Approved green, Expired slate, Cancelled red, each with an icon. Run prompt 5c to verify contrast before locking.

---

## 6. Payment gateway spec (simulated)

A gateway *interface* with a fake implementation, so the architecture looks real and the demo is deterministic.

**Flow**
1. Transaction page → **Pay now** (shows remaining balance; the amount is editable for partial payments).
2. `POST /transactions/{transaction}/pay` validates, then asks the gateway to create a checkout session. This returns a `gateway_reference` such as `SIM-9F3K2L`.
3. Redirect to `GET /gateway/checkout/{gateway_reference}`, a fake hosted page that looks like a separate payment provider: summary, method selector (GCash, Card, Bank transfer, Cash at counter), and a **Simulate outcome** selector (Success / Failed).
4. Submit → `POST /gateway/callback` calls `PaymentController@handleCallback`, which records the Payment and updates the transaction.
5. Redirect back to the transaction with a flash message.

**Rules (all enforced server-side)**
- Transaction must exist; not Cancelled; not already Paid/Approved.
- Amount must be numeric, > 0, and ≤ remaining balance.
- Remaining balance = `amount_due` − sum of **succeeded** payments.
- On Success: insert payment (`status = succeeded`), then if the balance is 0 set the transaction to Paid. All inside `DB::transaction()`.
- On Failed: insert payment (`status = failed`), leave transaction status unchanged.
- `gateway_reference` is unique, so a repeated callback can't double-record the same payment (idempotent).
- **Never collect, validate, or store card numbers or CVVs.** The simulated page only has a method selector and an outcome selector.

**Structure**
```text
app/Services/Payments/PaymentGateway.php            (interface: createCheckout, resolve)
app/Services/Payments/SimulatedGateway.php          (implementation)
app/Http/Controllers/PaymentController.php          (initiate, checkout page, callback)
```
Bind the interface in a service provider. For the demo, say plainly: "the gateway is simulated; swapping in a real provider means writing one new class that implements the interface."

---

## 7. GSAP spec

Install: `npm i gsap`, import in `resources/js/motion.js`, load from `app.js`.

**Where to use it (and nothing else)**

| Where | Effect | Duration |
| --- | --- | --- |
| Page load | Stagger-in of cards and table rows | 0.4 s, 0.05 s stagger |
| Dashboard numbers | Count-up from `data-count-to` | 0.9 s |
| Flash messages | Slide in, auto-dismiss | 0.3 s |
| Status badge after a change | Brief scale pulse | 0.3 s |
| Gateway checkout | Step timeline: Processing → Confirmed/Failed | 1-1.5 s total |
| Receipt | Fade/slide in | 0.4 s |

**Rules**
- Content must be visible if JS fails: use `gsap.from()` on elements that are visible by default. Never hide them in CSS first.
- Wrap everything in `gsap.matchMedia()` so reduced-motion users get no animation.
- Never animate form inputs, validation errors, or anything the user is trying to read or click right now.
- Keep all motion under 0.6 s except the count-up and gateway timeline. Easing from `DESIGN.md`.
- Drive it from data attributes (`data-animate="stagger"`, `data-count-to="123"`) so Blade views stay clean.
- Skip ScrollTrigger and plugins. Core GSAP is enough.

Starter shape:
```js
import gsap from 'gsap';

const mm = gsap.matchMedia();
mm.add('(prefers-reduced-motion: no-preference)', () => {
  gsap.from('[data-animate="stagger"] > *', {
    y: 12, opacity: 0, duration: 0.4, stagger: 0.05, ease: 'power2.out',
  });
  document.querySelectorAll('[data-count-to]').forEach((el) => {
    const end = Number(el.dataset.countTo);
    const state = { v: 0 };
    gsap.to(state, {
      v: end, duration: 0.9, ease: 'power1.out',
      onUpdate: () => { el.textContent = Math.round(state.v).toLocaleString(); },
    });
  });
});
```
Note the count-up must end on the exact server value (`data-count-to` and the initial text should both be the real number).

Motion prompt:
```text
Read AI_GUARDRAILS.md and PLAN.md section 7. Build resources/js/motion.js
per the table and rules. Use gsap.matchMedia for reduced motion, data
attributes for hooks, and add the data attributes to the existing Blade
views. Confirm: (1) with reduced motion on, nothing animates and all content
is visible, (2) with JS disabled, all content is visible, (3) count-up ends
on the exact server value. Don't touch business logic.
```

---

## 8. Rubric audit prompt (run at every gate)

```text
Read AI_GUARDRAILS.md and the rubric matrix in PLAN.md section 1.
Audit the current code against every row. For each of the 8 criteria give:
PASS / PARTIAL / FAIL, the specific evidence (file and behavior you
actually verified, not assumptions), and the smallest fix for anything
not PASS. Flag any code that violates the guardrails (business logic in
Blade, missing Form Request, duplicated code, hard delete, missing
validation). Then run a principle scan and list concrete findings with
file names: DRY (repeated status strings, duplicated Blade blocks or
validation rules, repeated queries that should be scopes), KISS
(unnecessary abstractions, long or deeply nested methods), WYSIWYG
(anything shown on screen or in the receipt that differs from what is
stored, hard-coded totals, blocked actions with no explanation, forms
that lose input on error). Do not praise. Do not suggest new features.
If you couldn't verify something, say "unverified".
```

---

## 9. If you're behind: cut in this order

1. GSAP (keep only the count-up, or drop entirely)
2. Receipt print styling (keep the page)
3. Level and category search filters (keep name search)
4. Level `benefits` field display
5. Gateway fake-checkout page (fall back to a plain payment form that posts to the same callback)

**Never cut:** member CRUD with validation, transaction/payment flow, approve-only-if-paid, history, relationships, README with ERD and screenshots.

---

## 10. Demo script and final checklist

1. Log in as admin → dashboard shows seeded numbers.
2. Categories and Levels pages → try deleting one that's in use (blocked, friendly error).
3. Register a member with invalid data (errors), then valid data (membership number generated).
4. Search by name, then filter by category and level.
5. Create an application transaction → reference number generated, status Pending.
6. Try **Approve** → blocked.
7. Pay part of the balance via the gateway → still Pending.
8. Pay the rest → Paid. Run a failed payment too → recorded as failed, status unchanged.
9. Approve → Approved.
10. Open member history → all transactions and payments listed.
11. Renew → new expiry dates correct.
12. Print the receipt.
13. Show the ERD and README.

Before submitting: `migrate:fresh --seed` on a clean clone works, `.env.example` is complete, screenshots are in `/docs`, and the admin credentials are in the README.
