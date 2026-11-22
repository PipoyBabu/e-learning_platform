# Axiom E-Learning Platform

## Membership Management and Transaction Processing System

This system manages member registration, membership categories, membership levels, and membership-related transactions such as applications, renewals, and payments.

## Main Features

### Member Management (CRUD)

- Add new member
- View member profile
- Edit member details
- Delete/deactivate member
- Search members by name, category, or level

### Membership Category and Level

- Categories: Student, Regular, Senior Citizen, Corporate
- Levels: Basic, Silver, Gold, Platinum
- Each level may have a fee, validity period, and benefits.

### Transaction Processing System (TPS)

- Create membership application transaction
- Process membership renewal
- Record payment transactions
- Generate official transaction/reference number
- Track transaction status: Pending, Paid, Approved, Expired, Cancelled
- View transaction history per member

## Database Connection

Local development currently uses SQLite so the project can run without a separate database server. The planned MySQL database name is `axiom_membership` for environments that use MySQL.

### Suggested Tables

- `users`
- `members`
- `membership_categories`
- `membership_levels`
- `membership_transactions`
- `payments`

## Required Laravel Components

- **Models:** `Member`, `MembershipCategory`, `MembershipLevel`, `MembershipTransaction`, `Payment`
- **Controllers:**
  - `MemberController`
  - `CategoryController`
  - `LevelController`
  - `TransactionController`
  - `PaymentController`
- Migrations for all database tables
- Blade views for forms, lists, details, and dashboards
- Laravel validation for required fields, email format, duplicate membership number, and payment amount
- Authentication using Laravel's built-in authentication system

## Suggested Pages

- Login page
- Dashboard with total members, active members, pending transactions, and collected payments
- Members list and member registration form
- Membership Categories page
- Membership Levels page
- Transactions page
- Payment recording page
- Member transaction-history page
- Printable membership record or receipt

## Functional Requirements

- The administrator can manage categories and levels.
- The administrator can register, edit, view, and deactivate members.
- A member must belong to one category and one membership level.
- The system must create a unique membership number.
- The system must record new membership and renewal transactions.
- Payments must be connected to a valid transaction.
- Only paid transactions may be marked as approved.
- The system must display transaction history and payment details.

## Rubric

| Criteria | Excellent | Good | Developing | Points |
| --- | --- | --- | --- | ---: |
| Laravel Setup and Database Connection | Application runs correctly; database, migrations, and relationships work fully | Minor setup issues | Major connection or migration issues | 15 |
| CRUD: Members | Complete Create, Read, Update, Delete, validation, and search | Most CRUD functions work | Incomplete CRUD functions | 20 |
| Category and Level Management | Complete CRUD with correct member assignment | Minor missing functions | Categories/levels not properly connected | 15 |
| Transaction Processing | Membership creation, renewal, payment, status, and history work correctly | Some transaction features work | Transactions are incomplete or inaccurate | 20 |
| Database Design | Proper tables, keys, relationships, and normalization | Minor design issues | Poor or missing relationships | 10 |
| Interface and Usability | Clean, responsive, easy-to-use interface | Usable but needs improvement | Difficult to use or incomplete | 10 |
| Code Quality | Organized MVC structure, readable code, proper validation | Some organization issues | Poor structure or duplicated code | 5 |
| Documentation and Presentation | Complete documentation, screenshots, and clear demonstration | Some documentation missing | Little to no documentation | 5 |
| **Total** |  |  |  | **100** |
