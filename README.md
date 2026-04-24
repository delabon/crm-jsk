# CRM-JSK

A full-featured **Customer Relationship Management (CRM)** web application built with Laravel 13, Inertia.js 3, and React. Designed to help sales teams manage their entire pipeline from first contact to closed deal — with a fast, SPA-like experience and no separate API.

The system tracks companies (accounts), the people inside them (contacts), and the sales opportunities attached to those people (deals). It supports marketing campaigns with contact enrollment, polymorphic tasks and notes across every entity, and a flexible tagging system — all rendered through server-driven Inertia pages with role-scoped data.

**Who uses it:**

- **Admins** — manage users, roles, and system-wide data
- **Managers** — oversee their team's contacts, deals, and pipeline metrics
- **Sales agents** — manage their own assigned contacts and deals
- **Users** — basic access for non-sales staff (dashboard + own profile only)

**Key architectural decisions:**

- Session-based auth via Laravel's built-in auth — no token layer needed with Inertia
- Inertia handles all page transitions — no REST API, no JSON endpoints for the frontend
- Leads are not a separate table — they are contacts with `status = 'lead'`
- Clients are not a separate table — they are contacts whose deal reached `closed_won`
- All polymorphic models (notes, tasks, tags) are reusable across any entity with zero new tables
- All data visibility is enforced at the query and Inertia props level via Spatie permissions

---

## Tech Stack

- **Backend:** PHP 8.5, Laravel 13, Spatie Laravel Permission
- **Frontend:** React 19, Inertia.js 3, Tailwind 4, Vite 7, TypeScript
- **UI Components:** Radix UI, Headless UI, Lucide Icons
- **Database:** PostgreSQL 18+, Redis (queues + cache)
- **Broadcasting:** Laravel Reverb
- **Search:** Laravel Scout
- **Testing:** Pest 4
- **Code Quality:** Pint, Larastan (PHPStan)
- **Dev Tools:** Sail (Docker), Telescope, Horizon, Pail

---

## Installation

### Clone the repo

```bash
git clone git@github.com:delabon/crm-jsk.git
cd crm-jsk
```

### Setup

```bash
composer install
vendor/bin/sail up --build -d
cp .env.example .env
vendor/bin/sail artisan key:generate
```

### Run the migration scripts

```bash
vendor/bin/sail artisan migrate
vendor/bin/sail artisan db:seed
```

### Build the assets

```bash
vendor/bin/sail npm install
vendor/bin/sail npm run build
```

### Start the queue worker

```bash
vendor/bin/sail artisan queue:work -v
```

---

## Check out the app

http://localhost/

To sign in, make sure you ran the database seeder (see installation step above), then use one of the following accounts:

| Role | Email | Password |
|---|---|---|
| Super Admin | super.admin@example.com | password |
| Manager | manager@example.com | password |
| Sales Agent | sales.agent@example.com | password |

http://localhost/login

Mailpit (emails): http://localhost:8025/
Telescope: http://localhost/telescope
Horizon: http://localhost/horizon

---

## Development

Start all dev services (server, queue, logs, Vite) concurrently:

```bash
vendor/bin/sail composer run dev
```

---

## Testing & Code Quality

### Run all checks

```bash
vendor/bin/sail composer test
```

### Run individually

```bash
vendor/bin/sail composer test:pest    # Pest tests
vendor/bin/sail composer test:stan    # Larastan (PHPStan)
vendor/bin/sail composer test:pint    # Pint (code style)
```

### Frontend

```bash
vendor/bin/sail npm run lint:check      # ESLint
vendor/bin/sail npm run format:check    # Prettier
vendor/bin/sail npm run types:check     # TypeScript
```
