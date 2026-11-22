# Axiom Design Decisions

## Direction

**Learning Journey Dashboard** — a friendly, educational membership workspace that makes membership level, payment progress, and transaction status easy to understand.

## Typography

- Heading and body font: Instrument Sans
- Reference numbers: system monospace stack
- Keep the existing Vite/Tailwind font setup during the core build.

## Palette

- Primary: Indigo `#4F46E5`
- Primary hover: Indigo `#4338CA`
- Learning accent: Sky `#0EA5E9`
- Warm accent: Amber `#F59E0B`
- Background: Slate `#F8FAFC`
- Surface: White `#FFFFFF`
- Border: Slate `#E2E8F0`
- Text: Slate `#0F172A`
- Muted text: Slate `#64748B`
- Success: Emerald `#059669`
- Warning: Amber `#D97706`
- Danger: Red `#DC2626`

## Interface patterns

- Dashboard cards show membership counts, payment totals, and pending work.
- Dashboard KPI cards are supported by lightweight charts: membership activity by month and successful payments over time.
- Charts should be server-driven and use an informative empty state when demo data is insufficient.
- Member detail pages show level, expiry, and payment progress as understandable summaries.
- Transactions use a visible status timeline: `Pending → Paid → Approved`.
- Remaining balances use progress bars only as a supplement; the numeric amount is always visible.
- Empty states explain what the administrator can do next.
- Tables remain available for efficient administrative work and receive horizontal scrolling on small screens.

## Interaction rules

- Keep the existing direct CRUD workflows for speed and reliability.
- Use confirmation before payments, approval, cancellation, and deactivation.
- Show a clear success or error message after every state-changing action.
- Use labels and text alongside color for status; never rely on color alone.
- Use a small amount of progressive enhancement only; all important content works without JavaScript.

## Motion personality

- Friendly and subtle: short fade/slide transitions and count-up metrics.
- No decorative motion that delays forms or hides transaction information.
- Respect `prefers-reduced-motion`.

## Scope guardrail

The learning-oriented styling should not become a full student portal. This remains an administrator-facing membership and transaction system.
