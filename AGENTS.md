<laravel-boost-guidelines>
=== .ai/plan rules ===

# Laravel 13 CRM — Build Plan

> 8 phases · 1–2 hrs daily

---

## About this project

A full-featured **Customer Relationship Management (CRM)** web application built with Laravel 13, Inertia.js 3, and React. Designed to help sales teams manage their entire pipeline from first contact to closed deal — with a fast, SPA-like experience and no separate API.

The system tracks companies (accounts), the people inside them (contacts), and the sales opportunities attached to those people (deals). It supports marketing campaigns with contact enrollment, polymorphic tasks and notes across every entity, and a flexible tagging system — all rendered through server-driven Inertia pages with role-scoped data.

**Who uses it:**
- **Admins** — manage users, roles, and system-wide data
- **Managers** — oversee their team's contacts, deals, and pipeline metrics
- **Sales agents** — manage their own assigned contacts and deals
- **Users** — basic access for non-sales staff (dashboard + own profile only)

**Tech stack:** Laravel 13 · PHP 8.2+ · Inertia.js 3 · React · Vite · Spatie Laravel Permission · PostgreSQL 18+ · Redis (queues + cache) · Laravel Reverb (broadcasting)

**Key architectural decisions:**
- Session-based auth via Laravel's built-in auth — no token layer needed with Inertia
- Inertia handles all page transitions
- Leads are not a separate table — they are contacts with `status = 'lead'`
- Clients are not a separate table — they are contacts whose deal reached `closed_won`
- All polymorphic models (notes, tasks, tags) are reusable across any entity with zero new tables
- All data visibility is enforced at the query and Inertia props level via Spatie permissions

---

## Phase 1 — Foundation & Auth ✅

> Users · Spatie roles · Inertia session auth

Already completed. Users table with `manager_id` self-join, Spatie installed, roles seeded (admin / manager / sales_agent / user), permissions defined per resource.

- [x] Create `users` migration with `manager_id` self-join
- [x] Install & configure `spatie/laravel-permission`
- [x] `RolesAndPermissionsSeeder` — all CRM permissions
- [x] Assign roles: `admin` / `manager` / `sales_agent` / `user`
- [x] Install & configure Inertia.js 3 with React adapter
- [x] Configure Vite for React + Inertia
- [x] Register `HasRoles` trait on `User` model
- [x] `User` model: `manager()`, `subordinates()` relationships

### Permissions defined

| Resource | Permissions |
|---|---|
| Dashboard | `view` |
| Profile | `manage` |
| Contacts | `view-any`, `view-own`, `create`, `update`, `delete` |
| Accounts | `view-any`, `view-own`, `create`, `update`, `delete` |
| Deals | `view-any`, `view-own`, `create`, `update`, `delete` |
| Campaigns | `view`, `manage` |
| Tasks | `manage` |
| Users | `manage` |
| Reports | `own`, `team`, `global` |

### Role matrix

| Permission | user | sales_agent | manager | admin |
|---|---|---|---|---|
| `dashboard.view` | ✅ | ✅ | ✅ | ✅ |
| `profile.manage` | ✅ | ✅ | ✅ | ✅ |
| `contacts.view-own` | ❌ | ✅ | ✅ | ✅ |
| `contacts.view-any` | ❌ | ❌ | ✅ | ✅ |
| `contacts.create` | ❌ | ✅ | ✅ | ✅ |
| `contacts.update` | ❌ | ✅ | ✅ | ✅ |
| `contacts.delete` | ❌ | ❌ | ✅ | ✅ |
| `accounts.view-own` | ❌ | ✅ | ✅ | ✅ |
| `accounts.view-any` | ❌ | ❌ | ✅ | ✅ |
| `accounts.create` | ❌ | ✅ | ✅ | ✅ |
| `accounts.update` | ❌ | ✅ | ✅ | ✅ |
| `accounts.delete` | ❌ | ❌ | ✅ | ✅ |
| `deals.view-own` | ❌ | ✅ | ✅ | ✅ |
| `deals.view-any` | ❌ | ❌ | ✅ | ✅ |
| `deals.create` | ❌ | ✅ | ✅ | ✅ |
| `deals.update` | ❌ | ✅ | ✅ | ✅ |
| `deals.delete` | ❌ | ❌ | ✅ | ✅ |
| `campaigns.view` | ❌ | ✅ | ✅ | ✅ |
| `campaigns.manage` | ❌ | ❌ | ✅ | ✅ |
| `tasks.manage` | ❌ | ✅ | ✅ | ✅ |
| `reports.own` | ❌ | ✅ | ✅ | ✅ |
| `reports.team` | ❌ | ❌ | ✅ | ✅ |
| `reports.global` | ❌ | ❌ | ❌ | ✅ |
| `users.manage` | ❌ | ❌ | ❌ | ✅ |

---

## Phase 2 — Accounts & Contacts ⬅️ Up Next

> Companies · People · Profiles · Countries

A `User` (sales rep) owns many Accounts and many Contacts independently. An Account is a company. A Contact is a person — they may work at an Account, or exist standalone (freelancer, individual). Status on Contact drives the lead → prospect → client funnel.

### Ownership model

```
User (sales rep)
 ├── hasMany Accounts   (companies they manage)
 └── hasMany Contacts   (people they manage)
       └── belongsTo Account (nullable — the company they work at)
```

The rep owns the contact directly via `contacts.user_id`. The account is just context. This means a rep can manage a contact who works at an account owned by a different rep — which is intentional and realistic.

A "lead" is a contact with `status = 'lead'`. No separate leads table needed — status scopes handle the funnel.

### FK layout

```
accounts: id, user_id, name, industry, website, phone
contacts: id, user_id, account_id (nullable), first_name, last_name, email (nullable), phone, status
profiles: id, contact_id, country_id, linkedin, avatar, job_title, bio

addresses: id, addressable_id, addressable_type, name, line1, line2,
           city, state, postal_code, country_id               ← poly
           (e.g. name = "HQ", "Billing", "Warehouse")
```

### Tasks

- [ ] `countries` migration (`id`, `name`, `code`)
- [ ] `accounts` migration (`id`, `user_id`, `name`, `industry`, `website`, `phone`)
- [ ] `contacts` migration (`id`, `user_id`, `account_id` nullable, `first_name`, `last_name`, `email` nullable, `phone`, `status`)
- [ ] `profiles` migration (`id`, `contact_id`, `country_id`, `linkedin`, `avatar`, `job_title`, `bio`)
- [ ] `addresses` migration with `morphs('addressable')`, `name`, `line1`, `line2`, `city`, `state`, `postal_code`, `country_id`
- [ ] `User` model: `hasMany(Account)`, `hasMany(Contact)`
- [ ] `Account` model: `belongsTo(User)`, `hasMany(Contact)`, `morphMany(Address)`
- [ ] `Contact` model: `belongsTo(User)`, `belongsTo(Account)` nullable
- [ ] `Contact` model: `hasOne(Profile)`, `hasMany(Deal)`, `morphOne(Address)`
- [ ] `Contact` model: `hasOneThrough(Country via Profile)`
- [ ] `Contact` model: `morphOne(Note)`, `morphMany(Task)`, `morphToMany(Tag)`
- [ ] `Address` model: `morphTo()`, `belongsTo(Country)`
- [ ] `Profile` model: `belongsTo(Contact)`, `belongsTo(Country)`
- [ ] `ContactPolicy`: `view-own` vs `view-any` via Spatie
- [ ] `AccountPolicy`: `view-own` vs `view-any` via Spatie, `delete` locked to manager+
- [ ] `ListContactsAction`, `CreateContactAction`, `UpdateContactAction`, `DeleteContactAction`
- [ ] `ListAccountsAction`, `CreateAccountAction`, `UpdateAccountAction`, `DeleteAccountAction`
- [ ] `ContactFactory` + `AccountFactory` with Faker
- [ ] `AccountSeeder` + `ContactSeeder` with profiles and addresses

### Relationships in this phase

| Type | From | To | Notes |
|---|---|---|---|
| One-to-Many | `User` | `Account` | rep owns many companies |
| One-to-Many | `User` | `Contact` | rep owns many people |
| One-to-Many | `Account` | `Contact` | company has many employees |
| Many-to-One | `Contact` | `Account` | nullable — person may have no company |
| One-to-One | `Contact` | `Profile` | enriched contact data |
| Has One Through | `Contact` | `Country` | via `Profile.country_id` |
| Poly One-to-Many | `Account` | `Address` | labeled addresses e.g. HQ, Billing |
| Poly One-to-One | `Contact` | `Address` | single personal address |

---

## Phase 3 — Deals & Pipeline

> Deals · Stages · Pipeline value · Won/Lost

Deals are the core revenue engine. Each deal belongs to a contact and a user (rep). Stage drives the pipeline view. `HasManyThrough` lets managers see all their team's deals in one call.

### Tasks

- [ ] `deals` migration (`id`, `contact_id`, `user_id`, `title`, `value`, `stage`, `expected_close`)
- [ ] `Deal` model: `belongsTo(Contact)`, `belongsTo(User)`
- [ ] `Deal` model: `morphOne(Note)`, `morphMany(Task)`, `morphToMany(Tag)`
- [ ] `User` model: `hasManyThrough(Deal via Contact)`
- [ ] `DealPolicy`: `view-own` vs `view-any` (Spatie)
- [ ] Scoped queries: `openDeals()`, `wonDeals()`, pipeline value sum
- [ ] `DealFactory` + `DealSeeder` across contacts
- [ ] `chunkById()` for bulk deal processing jobs

### Stage enum

```
new → qualified → proposal → negotiation → closed_won / closed_lost
```

---

## Phase 4 — Campaigns & Enrollment

> Campaigns · N–N pivot · Enrollment status

Campaigns target many contacts. The `campaign_contact` pivot stores enrollment status and date. Managers and admins manage campaigns; agents can only view them.

### Tasks

- [ ] `campaigns` migration (`id`, `name`, `type`, `starts_at`, `ends_at`)
- [ ] `campaign_contact` pivot (`campaign_id`, `contact_id`, `status`, `enrolled_at`)
- [ ] `Campaign` model: `belongsToMany(Contact)` with pivot
- [ ] `Contact` model: `belongsToMany(Campaign)` with pivot
- [ ] `Campaign` model: `morphMany(Task)`, `morphToMany(Tag)`
- [ ] `CampaignPolicy`: `campaigns.manage` permission gate
- [ ] Enrollment logic: `attach`, `sync`, `updateExistingPivot`
- [ ] `CampaignFactory` + enrollment seeder

### Pivot columns

```
campaign_contact: campaign_id, contact_id, status (enrolled/converted/unsubscribed), enrolled_at
```

---

## Phase 5 — Notes, Tasks & Tags

> Polymorphic 1–1 · 1–N · N–N across all models

These three are fully polymorphic — they attach to Contacts, Deals, and Campaigns with zero new tables per model added.

### Tasks

- [ ] `notes` migration with `morphs('notable')`
- [ ] `tasks` migration with `morphs('taskable')` + `priority` + `due_at`
- [ ] `tags` migration + `taggables` pivot with `morphs('taggable')`
- [ ] `Note` model: `morphTo()` + `belongsTo(User)`
- [ ] `Task` model: `morphTo()` + `belongsTo(User)`
- [ ] `Tag` model: `morphedByMany()` for contacts / deals / campaigns
- [ ] `tasks.manage` permission enforced in `TaskPolicy`
- [ ] Global "my tasks" query: tasks scoped to auth user across all morphable models
- [ ] `chunkById()` for overdue task notifications job

### Polymorphic map

| Type | Relationship | Models |
|---|---|---|
| Poly 1–1 | `morphOne(Note)` | Contact, Deal |
| Poly 1–N | `morphMany(Task)` | Contact, Deal, Campaign |
| Poly N–N | `morphToMany(Tag)` | Contact, Deal, Campaign |

---

## Phase 6 — Inertia UI Layer

> Controllers · Inertia props · React pages · Role-scoped rendering

Controllers return `Inertia::render()` with props scoped to the authenticated user's permissions. React components receive typed props and never fetch data independently — all data flows through the Inertia page response.

### Tasks

- [ ] `ContactController`: index props scoped by `view-own` vs `view-any`
- [ ] `ContactController`: create / edit / destroy with policy guards
- [ ] `AccountController`: full CRUD with Inertia responses
- [ ] `DealController`: index with pipeline summary props for dashboard
- [ ] `CampaignController`: enroll / unenroll actions via Inertia forms
- [ ] `TaskController`: my-tasks page with morphable parent context
- [ ] Share auth user + permissions to all pages via `HandleInertiaRequests`
- [ ] Route groups with Spatie `role` / `permission` middleware
- [ ] React: `Contacts/Index`, `Contacts/Show`, `Contacts/Create`, `Contacts/Edit`
- [ ] React: `Deals/Index` with pipeline kanban or list view
- [ ] React: `Campaigns/Index` with enrollment actions
- [ ] React: `Tasks/Index` — my tasks across all models
- [ ] React: `Dashboard` — summary cards scoped by role
- [ ] React: `Profile/Edit` — available to all roles

### Action Pattern

Each user action maps to a dedicated action class under `app/Actions/`. Controllers stay thin — they authorize, invoke the action, and return the Inertia response.

```
app/Actions/Contacts/
    ListContactsAction.php
    CreateContactAction.php
    UpdateContactAction.php
    DeleteContactAction.php
```

```php
// app/Actions/Contacts/ListContactsAction.php
class ListContactsAction
{
    public function handle(User $user): LengthAwarePaginator
    {
        return Contact::query()
            ->when(
                $user->cannot('contacts.view-any'),
                fn($q) => $q->where('user_id', $user->id)
            )
            ->with(['profile.country', 'account', 'tags'])
            ->paginate(25);
    }
}

// app/Http/Controllers/ContactController.php
class ContactController extends Controller
{
    public function index(ListContactsAction $action): Response
    {
        $this->authorize('contacts.view-own');

        return Inertia::render('Contacts/Index', [
            'contacts' => ContactResource::collection(
                $action->handle(auth()->user())
            ),
            'can' => [
                'create' => auth()->user()->can('contacts.create'),
                'delete' => auth()->user()->can('contacts.delete'),
            ],
        ]);
    }

    public function store(StoreContactRequest $request, CreateContactAction $action): RedirectResponse
    {
        $this->authorize('contacts.create');

        $action->handle(auth()->user(), $request->validated());

        return redirect()->route('contacts.index');
    }
}
```

### Sharing permissions globally via middleware

```php
// HandleInertiaRequests.php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user'        => $request->user(),
            'roles'       => $request->user()?->getRoleNames(),
            'permissions' => $request->user()?->getAllPermissions()->pluck('name'),
        ],
    ];
}
```

---

## Phase 7 — Queues & Events

> Jobs · Events · Notifications · Broadcasting

Background processing keeps page responses fast. Deal stage changes fire events. Overdue tasks run on a schedule. `chunkById()` is used throughout for safe bulk processing. Reverb broadcasts real-time updates to React components via Laravel Echo.

### Tasks

- [ ] `DealStageChanged` event + listener (log, notify manager)
- [ ] `ContactAssigned` event when rep changes
- [ ] `OverdueTasksJob`: `chunkById()` across tasks table
- [ ] `CampaignEnrollmentJob`: bulk attach contacts
- [ ] Failover queue driver configured (Laravel 13)
- [ ] Mail notification: deal won → send summary to manager
- [ ] Database notification: task assigned to user
- [ ] Schedule: daily overdue task digest at 08:00
- [ ] Laravel Reverb: broadcast deal stage changes to dashboard in real time
- [ ] React: Laravel Echo listener for live deal updates

### Queue note

Use `chunkById()` — not `chunk()` (OFFSET degrades at scale) and not `cursor()` (slow, long-lived DB connection).

```php
Task::where('completed', false)
    ->where('due_at', '<', now())
    ->chunkById(500, function ($tasks) {
        $tasks->each(fn($t) => SendOverdueNotification::dispatch($t));
    });
```

---

## Phase 8 — Testing

> Feature tests · Policies · Factories · Inertia assertions

Every permission boundary needs a test. The most critical tests are role-scoped — assert that a `sales_agent` truly cannot see another rep's contacts or deals. Inertia's test helpers let you assert page components and props directly.

### Tasks

- [ ] `ContactTest`: agent sees own contacts only
- [ ] `ContactTest`: manager sees team contacts
- [ ] `DealTest`: pipeline value aggregation
- [ ] `CampaignTest`: enroll / unenroll / pivot status
- [ ] `TaskTest`: polymorphic attach to contact, deal, campaign
- [ ] `TagTest`: `morphToMany` sync across models
- [ ] Auth: assert 403 on forbidden role actions
- [ ] Inertia: assert correct component and props returned per role
- [ ] Queue fakes for `DealStageChanged` event
- [ ] Mail fake for deal won notification
- [ ] Full `DatabaseSeeder` runs without errors

### Test pattern for role scoping with Inertia

```php
// New users are automatically assigned the 'user' role.
// syncRoles() replaces all roles, so it correctly overrides the default.

it('sales agent cannot view other reps contacts', function () {
    $agent1 = User::factory()->create()->syncRoles(['sales_agent']);
    $agent2 = User::factory()->create()->syncRoles(['sales_agent']);
    $contact = Contact::factory()->create(['user_id' => $agent2->id]);

    actingAs($agent1)
        ->get("/contacts/{$contact->id}")
        ->assertForbidden();
});

it('renders correct Inertia component with scoped props', function () {
    $agent = User::factory()->create()->syncRoles(['sales_agent']);
    Contact::factory(3)->create(['user_id' => $agent->id]);
    Contact::factory(2)->create(); // other rep's contacts — should not appear

    actingAs($agent)
        ->get('/contacts')
        ->assertInertia(fn ($page) => $page
            ->component('Contacts/Index')
            ->has('contacts.data', 3)
            ->has('can', fn ($can) => $can
                ->where('create', true)
                ->where('delete', false)
            )
        );
});

it('manager sees all contacts across team', function () {
    $manager = User::factory()->create()->syncRoles(['manager']);
    User::factory(2)->create()->each(fn($u) => $u->syncRoles(['sales_agent']));
    Contact::factory(5)->create();

    actingAs($manager)
        ->get('/contacts')
        ->assertInertia(fn ($page) => $page
            ->component('Contacts/Index')
            ->has('contacts.data', 5)
        );
});
```

---

## Schema overview

```
users           id, name, email, password, manager_id (self-join)
  ↳ roles       via Spatie model_has_roles pivot

accounts        id, user_id, name, industry, website, phone
contacts        id, user_id, account_id (nullable), first_name, last_name, email (nullable), phone, status
profiles        id, contact_id, country_id, linkedin, avatar, job_title, bio
countries       id, name, code
addresses       id, name, line1, line2, city, state, postal_code, country_id,
                addressable_id, addressable_type                ← poly
                (Account → morphMany, Contact → morphOne)

deals           id, contact_id, user_id, title, value, stage, expected_close
campaigns       id, name, type, starts_at, ends_at
campaign_contact  campaign_id, contact_id, status, enrolled_at  ← N–N pivot

notes           id, user_id, body, pinned, notable_id, notable_type    ← poly
tasks           id, user_id, title, priority, completed, due_at,
                taskable_id, taskable_type                              ← poly
tags            id, name, color
taggables       tag_id, taggable_id, taggable_type                     ← poly pivot
```

---

## Key rules to remember

- **`chunkById()` over `chunk()`** — avoids OFFSET degradation on large tables
- **Check permissions, not roles** — `$user->can('contacts.delete')` survives role restructuring
- **`users` ≠ `contacts`** — users log in and operate the CRM; contacts are business data
- **Leads are not a table** — they are contacts with `status = 'lead'`
- **Always reset Spatie cache in production** — `php artisan permission:cache-reset`
- **Polymorphic morph name must match** across `morphTo`, `morphOne`, `morphMany`, `morphToMany`
- **Scope data in the controller, not the React component** — never trust the frontend to filter; always enforce permissions server-side before passing props to `Inertia::render()`
- **Share only what the role needs** — pass `can` arrays as Inertia props to drive UI conditionally without exposing the full permission set

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/framework (LARAVEL) - v13
- laravel/horizon (HORIZON) - v5
- laravel/prompts (PROMPTS) - v0
- laravel/scout (SCOUT) - v11
- laravel/wayfinder (WAYFINDER) - v0
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- laravel/telescope (TELESCOPE) - v5
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/react (INERTIA_REACT) - v3
- react (REACT) - v19
- tailwindcss (TAILWINDCSS) - v4
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `vendor/bin/sail npm run build`, `vendor/bin/sail npm run dev`, or `vendor/bin/sail composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `vendor/bin/sail artisan route:list`). Use `vendor/bin/sail artisan list` to discover available commands and `vendor/bin/sail artisan [command] --help` to check parameters.
- Inspect routes with `vendor/bin/sail artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `vendor/bin/sail artisan config:show app.name`, `vendor/bin/sail artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `vendor/bin/sail artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `vendor/bin/sail artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `vendor/bin/sail artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `vendor/bin/sail artisan list` and check their parameters with `vendor/bin/sail artisan [command] --help`.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `vendor/bin/sail artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `vendor/bin/sail artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `vendor/bin/sail artisan make:test --pest SomeFeatureTest` instead of `vendor/bin/sail artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `vendor/bin/sail artisan test --compact` or filter: `vendor/bin/sail artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

</laravel-boost-guidelines>
