# 8bit Laravel Starter

A production-ready Laravel application foundation built with Laravel, Livewire, Mary UI, Tailwind CSS, and daisyUI — with authentication, member and admin panels, roles & permissions, and user management already structured for extension.

This is a starter, not a finished application. It gives you a working authorization foundation and admin panel so new projects can start from a mature baseline instead of rebuilding the same scaffolding every time.

## Features

Everything below is implemented and working in this repository today — not a roadmap.

**Foundation**
- Laravel 13 on the TALL stack (**T**ailwind, **A**lpine.js, **L**aravel, **L**ivewire — Alpine ships bundled with Livewire)
- Livewire 4 as the primary interactive UI layer
- Mary UI, Tailwind CSS 4, and daisyUI 5 for a consistent, responsive, themeable component system with dark mode
- Vite for frontend asset bundling (`npm run build` / `npm run dev`)

**Authentication**
- Login, logout, and password reset
- Optional public registration — **disabled by default**, toggled in `config/features.php`
- Optional email-verification requirement — **disabled by default**, toggled in `config/features.php`
- Profile management — every user updates their own name and email from `/profile`
- Self-service password change — current password verified before a new one is accepted

**Member Panel**
- `/dashboard` and `/profile` behind a clean top-navigation layout with no sidebar — visually distinct from the Admin Panel

**Admin Panel**
- Separate `/admin/*` area with its own layout and a grouped, permission-aware sidebar
- Navigation items appear only when the current user holds the matching permission — never a static, one-size-fits-all menu

**Roles & Permissions Manager**
- Full Roles and Permissions CRUD in the Admin Panel, built on [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- Human-readable `{verb} {resource}` permission naming (`view users`, `create roles`, `access dashboard`, ...) — no dot notation, no separate internal identifier
- A fixed, developer-owned set of protected/system permissions (`config/permissions.php`) that can never be renamed, deleted, or duplicated through the UI, by anyone
- A protected Super Admin role that can never be renamed, deleted, or recreated through the UI
- A single, centralized `Gate::before` Super Admin bypass — no `hasRole('Super Admin')` or `isSuperAdmin()` checks scattered through the app
- Authorization enforced with Laravel-native `can:` route middleware and Blade `@can(...)` checks — UI visibility is never the only guard

**User Management**
- Create, edit, and delete users from the Admin Panel, with search and pagination
- Dynamic role assignment loaded from the database — the Super Admin role is only assignable by an existing Super Admin
- Built-in protection rules: a user can never delete their own account, and the last remaining Super Admin can never be deleted or demoted, including by themselves

**Bootstrapping**
- `php artisan 8bit:create-super-admin` — the only way to create the first Super Admin account

**Quality tooling**
- Pest 4 for testing, Laravel Pint for formatting, Larastan/PHPStan for static analysis, Vite for the frontend build

**Public release**
- MIT licensed
- Composer metadata (`name`, `description`, `type`, `keywords`) configured for Packagist and the `laravel new --using=...` installer workflow

### Planned / future modules

This starter intentionally stops at the authentication/authorization/admin foundation. Properties, Bookings, Calendar, Guests, Tours, Finance, and Settings are **not implemented** — they exist only as illustrative examples in this repository's internal design documents of the kind of business modules a project built on this starter is expected to add for itself.

## Requirements

- PHP **8.3+**
- Composer
- Node.js and NPM (for building frontend assets)
- A database supported by Laravel (MySQL, PostgreSQL, or SQLite)

Exact package versions are pinned in [`composer.json`](composer.json) and [`package.json`](package.json).

## Installation

### Using the Laravel Installer (recommended)

If you have the [Laravel installer](https://laravel.com/docs/installation#installing-php) installed, you can scaffold a new project directly from this starter:

```bash
laravel new my-project --using=8bit-technologies/8bit-laravel-starter
```

> **Note:** the `--using` flag installs a starter kit published on [Packagist](https://packagist.org). If this exact command isn't resolving yet, clone the repository directly using the instructions below instead — the result is identical.

### Manual installation

```bash
git clone https://github.com/8bit-Technologies/8bit-laravel-starter.git my-project
cd my-project

composer install
cp .env.example .env
php artisan key:generate

npm install
npm run build
```

### Required setup after installation

Once dependencies are installed and `.env` is configured (see [Environment Configuration](#environment-configuration) below), run:

```bash
php artisan migrate
php artisan db:seed
php artisan 8bit:create-super-admin
```

- `php artisan migrate` creates the application's tables, including the Spatie Permission tables.
- `php artisan db:seed` runs `RolePermissionSeeder`, which creates the starter's three seed roles (`Super Admin`, `Admin`, `Member`) and its thirteen core permissions. It creates no user accounts and is safe to run more than once.
- `php artisan 8bit:create-super-admin` is the **only** way to create the first Super Admin. It's an interactive console command — it will prompt you for a name, email, and password.

Then start the app:

```bash
composer run dev
```

This runs the Laravel dev server, queue listener, log viewer, and Vite dev server together. Visit `http://localhost:8000`.

## Environment Configuration

Configuration lives in `.env`, generated from `.env.example` during installation. Key values to review:

- `APP_NAME` — the application name shown in the UI and page titles.
- `APP_URL` — your local or deployed URL (e.g. `http://localhost:8000`).
- `DB_*` — your database connection. The starter works with MySQL, PostgreSQL, or SQLite; update the `DB_CONNECTION` and related variables to match your setup.
- `MAIL_*` — required once you rely on password reset or email verification emails outside of local log-driver testing.

Never commit your `.env` file — it's already excluded via `.gitignore`.

## Timezone

The starter follows Laravel's own configured application timezone, defined in `config/app.php`:

```php
'timezone' => 'UTC',
```

`UTC` is the default. If your project needs a different timezone, change this value directly, for example:

```php
'timezone' => 'Asia/Kolkata',
```

This is a one-line change in `config/app.php` after creating your project — there's nothing else to configure.

## Authentication

Authentication is built on Laravel's own primitives with Livewire components (`app/Livewire/Auth/`):

- `GET /login`, `POST /login` — session login
- `POST /logout` — logout
- `GET /register`, `POST /register` — registration (see below — disabled by default)
- `GET /forgot-password`, `POST /forgot-password` — password reset request
- `GET /reset-password/{token}`, `POST /reset-password` — password reset
- Email verification notice and verification link handling (see below — not required by default)

### Registration and email verification are optional

Not every application built on this starter wants public self-registration or mandatory email verification — many are internal or invite-only, with every account created by an administrator through User Management. Both behaviors are controlled independently through `config/features.php`:

```php
// config/features.php
return [
    'registration_enabled' => false,
    'email_verification_enabled' => false,
];
```

Both default to **disabled**. Nothing is deleted when disabled — the `Register` and `VerifyEmail` Livewire components, their views, and routes all remain in the codebase; only reachability changes.

**Registration** (`registration_enabled`): when disabled, `/register` returns a 404 and the "Sign up" link is hidden from the login page. Set it to `true` to restore both. This is enforced by a small `EnsureRegistrationIsEnabled` middleware on the `/register` route, so the check lives in one place rather than inside the Livewire component.

**Email verification** (`email_verification_enabled`): when disabled, `/dashboard`, `/profile`, and the entire Admin area (`/admin/*`) only require authentication (`auth`), matching Laravel's own convention of composing middleware. When enabled, they additionally require a verified email (`auth` + `verified`), exactly as Laravel ships by default. This is enforced by an `EnsureEmailIsVerifiedIfRequired` middleware that wraps Laravel's native `verified` middleware — when the feature is disabled it's a pass-through; when enabled it defers entirely to Laravel's own verification check. The Admin area's permission-based authorization (`can:access dashboard`, Roles/Permissions/User Management rules) is unaffected either way — this setting only ever adds or removes the verification requirement, never the authorization requirement.

## Member Panel

The Member Panel is the ordinary authenticated area available to any signed-in user, regardless of role or permissions:

- `/dashboard` — a minimal authenticated landing page
- `/profile` — the current user managing their own name, email, and password

It uses `resources/views/layouts/app.blade.php` — a simple top navigation bar (branding, a Dashboard link, a user dropdown with Profile/Logout, and a dark mode toggle). It intentionally has no sidebar, so it never looks like a smaller copy of the Admin Panel.

## Admin Panel

The Admin Panel (`/admin/*`) is the permission-gated management area, using `resources/views/layouts/admin.blade.php` and a grouped, permission-aware sidebar.

**The Admin Panel is not a separate application.** It's part of the same Laravel application, the same repository, and the same database as the Member Panel — the separation is purely at the routing/layout/authorization level (see `ARCHITECTURE.md`).

Currently implemented:

```text
Platform
    Dashboard

Users
    Users
    Roles
    Permissions
```

Each item is shown only when the current user holds the corresponding permission — see [Roles & Permissions](#roles--permissions) below. Additional groups (Accommodation, Finance, Settings, etc.) are added by individual projects as their own modules are built; the starter doesn't ship placeholder navigation for modules that don't exist yet.

## Roles & Permissions

Authorization is built on [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission).

### Naming convention

Permissions use human-readable `{verb} {resource}` names — not dot notation. The permission name **is** the Laravel authorization ability, with no separate internal identifier:

```text
access dashboard
view users
create users
update users
delete users
view roles
create roles
update roles
delete roles
view permissions
create permissions
update permissions
delete permissions
```

These thirteen are the starter's protected/system permissions (`config/permissions.php`) — they can never be renamed, deleted, or duplicated through the Admin UI, by anyone, including a Super Admin. Additional, project-specific permissions (`view bookings`, `create invoices`, ...) can be created freely through the Permissions Manager or a project seeder, following the same `{verb} {resource}` pattern.

### Checking permissions

The same string is used everywhere:

```php
// Route middleware
Route::middleware('can:view users')->group(...);

// Blade
@can('access dashboard')
    ...
@endcan

// PHP
$user->can('update roles');
```

### Super Admin

Super Admin is a protected system concept, not an ordinary role. It bypasses every permission check through a single, centralized `Gate::before` rule registered in `AppServiceProvider` — there are no scattered `hasRole('Super Admin')` or `isSuperAdmin()` checks throughout the application's authorization logic.

The first Super Admin is created through:

```bash
php artisan 8bit:create-super-admin
```

This is intentionally the only way to bootstrap the first Super Admin — there's no route or UI form that creates one, closing off the obvious self-escalation path. Once at least one Super Admin exists, an existing Super Admin can promote another user to Super Admin through User Management; this is still guarded server-side by an explicit `isSuperAdmin()` check on the acting user, not by hiding the option in the UI.

Super Admin protection — the protected role, the protected permissions, and the escalation guards — is handled deliberately at the application/authorization layer, documented in full in `PHASE-3-ROLES-PERMISSIONS.md`.

## User Management

Under **Admin → Users → Users**, an authorized administrator can:

- List, search, and paginate users, seeing each user's name, email, assigned role(s), email verification status, and creation date
- Create a user (name, email, password, and an optional role)
- Edit a user (name, email, role, and an optional password change — leaving the password blank keeps the existing one)
- Delete a user, with confirmation

Role options are loaded dynamically from the database — nothing is hard-coded — and the Super Admin role is only offered as an assignable option to an actor who is already a Super Admin. The last remaining Super Admin can never be deleted or stripped of the Super Admin role, including by themselves.

## Development

```bash
# Run the test suite
php artisan test

# Format code
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse

# Build frontend assets
npm run build

# Run everything together during development (server, queue, logs, Vite)
composer run dev
```

## Project Structure

```text
app/Livewire/
├── Auth/            # Login, registration, password reset, email verification
├── Admin/           # Admin Panel components (Dashboard, Roles, Permissions, Users)
├── Dashboard.php    # Member Panel dashboard
└── Profile.php      # Member Panel profile

resources/views/
├── layouts/
│   ├── guest.blade.php   # Guest / public-facing layout
│   ├── app.blade.php     # Member Panel layout (no sidebar)
│   └── admin.blade.php   # Admin Panel layout (grouped sidebar)
├── livewire/             # Views for the components above
└── components/8bit/      # Reusable application-level components

routes/
├── web.php     # Guest routes + Member Panel routes
├── auth.php    # Authentication routes
└── admin.php   # Admin Panel routes, under /admin

database/    # Migrations, factories, seeders
config/      # Application configuration, including config/permissions.php
```

The three application areas are a routing/layout/authorization distinction within one Laravel app, not separate codebases:

- **Guest** — unauthenticated, public-facing (`layouts/guest.blade.php`)
- **Member** — any authenticated user (`layouts/app.blade.php`, `/dashboard`, `/profile`)
- **Admin** — permission-gated (`layouts/admin.blade.php`, `/admin/*`)

## Architecture & Documentation

This repository includes its own internal design documentation:

- [`ARCHITECTURE.md`](ARCHITECTURE.md) — overall application architecture
- [`CONVENTIONS.md`](CONVENTIONS.md) — coding and naming conventions
- [`FEATURE-SET.md`](FEATURE-SET.md) — the starter's intended feature scope
- [`PROJECT-BOOTSTRAP.md`](PROJECT-BOOTSTRAP.md) — initial project bootstrap plan
- [`UI-DESIGN-SYSTEM.md`](UI-DESIGN-SYSTEM.md) — UI/UX design conventions
- [`PHASE-3-ROLES-PERMISSIONS.md`](PHASE-3-ROLES-PERMISSIONS.md) — the full roles/permissions/Super Admin design specification

These are worth reading before making architectural changes to a project built on this starter.

## Contributing

Issues and pull requests are welcome. Before submitting a change:

1. Follow the conventions in `CONVENTIONS.md` and `ARCHITECTURE.md`.
2. Add or update tests for any behavior change.
3. Run `php artisan test`, `vendor/bin/pint`, and `vendor/bin/phpstan analyse` before opening a pull request.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
