# 8bit Laravel Starter

A production-ready Laravel application foundation built with Laravel, Livewire, Mary UI, Tailwind CSS, and daisyUI — with authentication, member and admin panels, roles & permissions, and user management already structured for extension.

This is a starter, not a finished application. It gives you a working authorization foundation and admin panel so new projects can start from a mature baseline instead of rebuilding the same scaffolding every time.

## Features

- **Laravel 13** application foundation, built close to Laravel's own conventions
- **Livewire 4** as the primary interactive UI layer
- **Mary UI**, **Tailwind CSS 4**, and **daisyUI 5** for a consistent, themeable component system
- **Authentication** — login, registration, logout, password reset, and email verification
- **Member Panel** (`/dashboard`, `/profile`) — a clean authenticated area for any signed-in user
- **Admin Panel** (`/admin/*`) — a separate, permission-aware area with a grouped sidebar
- **Roles & Permissions** — built on [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission), with a dynamic Roles & Permissions manager
- **Super Admin bypass** — a single, centralized `Gate::before` rule, not scattered role checks
- **Protected permissions** — a fixed, developer-owned set of system permissions that can't be renamed, deleted, or hijacked through the UI
- **User Management** — create, edit, and manage users, with role assignment
- **Profile** — every authenticated user can manage their own name, email, and password
- **Responsive UI** with dark mode support throughout
- **Pest 4** for testing, **Laravel Pint** for formatting, **Larastan/PHPStan** for static analysis

This starter does not claim to be a full admin framework or a business application. It intentionally stops at the authorization/admin foundation — business modules (properties, bookings, billing, etc.) are for the project built on top of it.

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
- `GET /register`, `POST /register` — registration
- `GET /forgot-password`, `POST /forgot-password` — password reset request
- `GET /reset-password/{token}`, `POST /reset-password` — password reset
- Email verification notice and verification link handling

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
