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
- Inertia handles all page transitions — no REST API, no JSON endpoints for the frontend
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

Accounts are the companies. Contacts are the people inside them. A contact can exist without an account (freelancer, individual). Status on contact drives the lead → prospect → client funnel.

### Architecture note
```
users        → internal, log in, have roles
accounts     → companies (Acme Corp, Google, etc.)
contacts     → people, belong to an account, have a status (lead/prospect/client)
```

A "lead" is a contact with `status = 'lead'`. No separate leads table needed — status scopes handle the funnel.

### Tasks
- [ ] `countries` migration (`id`, `name`, `code`)
- [ ] `accounts` migration (`id`, `name`, `industry`, `website`, `user_id`)
- [ ] `contacts` migration (`id`, `user_id`, `account_id` nullable, `name`, `email`, `phone`, `status`)
- [ ] `profiles` migration (`id`, `contact_id`, `country_id`, `linkedin`, `avatar`, `job_title`, `bio`)
- [ ] `Account` model: `hasMany(Contact)`, `belongsTo(User)`
- [ ] `Contact` model: `belongsTo(Account)`, `hasOne(Profile)`, `hasMany(Deal)`
- [ ] `Contact` model: `hasOneThrough(Country via Profile)`
- [ ] `Contact` model: `morphOne(Note)`, `morphMany(Task)`, `morphToMany(Tag)`
- [ ] `Profile` model: `belongsTo(Contact)`, `belongsTo(Country)`
- [ ] `ContactPolicy`: `view-own` vs `view-any` via Spatie
- [ ] `AccountPolicy`: managers see all, agents see assigned only
- [ ] `ContactFactory` + `AccountFactory` with Faker
- [ ] `AccountSeeder` + `ContactSeeder` with profiles

### Relationships in this phase
| Type | Relationship |
|---|---|
| One-to-One | `Contact` → `Profile` |
| One-to-Many | `Account` → `Contacts` |
| Has One Through | `Contact` → `Country` (via `Profile`) |

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

accounts        id, user_id, name, industry, website
contacts        id, user_id, account_id (nullable), name, email, phone, status
profiles        id, contact_id, country_id, linkedin, avatar, job_title, bio
countries       id, name, code

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
