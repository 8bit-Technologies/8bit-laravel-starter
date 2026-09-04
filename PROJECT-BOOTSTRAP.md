# 8bit Laravel Starter v1
## Project Bootstrap Specification

**Organization:** 8bit Technologies  
**Starter:** 8bit Laravel Starter  
**Minimum PHP:** 8.3  
**Laravel:** 13  
**Livewire:** 4  
**UI:** Mary UI 2 + Tailwind CSS 4 + daisyUI 5

---

# 1. Purpose

This document defines the exact process for creating the **8bit Laravel Starter** from a fresh Laravel installation.

The objective is to produce a clean, working Laravel application that follows:

```text
PACKAGES.md
ARCHITECTURE.md
CONVENTIONS.md
UI-DESIGN-SYSTEM.md
FEATURE-SET.md
CLAUDE.md
```

The bootstrap process should be repeatable.

A fresh project following this document should produce substantially the same architecture and developer experience every time.

---

# 2. Bootstrap Philosophy

The bootstrap process follows:

> Install only what is required, configure it properly, verify it, then commit a clean baseline.

Do not add speculative features.

Do not perform unrelated customization.

Do not manually modify framework files unless required.

---

# 3. Prerequisites

Before starting, verify:

```text
PHP >= 8.3
Composer
Node.js
npm
Git
```

Recommended:

```text
MySQL or MariaDB
Redis
```

Redis is not mandatory for local development unless required by the project.

---

# 4. Verify PHP

Run:

```bash
php -v
```

The result must indicate PHP 8.3 or newer.

If PHP is below 8.3:

> Stop the bootstrap process.

Do not attempt to work around the minimum requirement.

---

# 5. Verify Composer

Run:

```bash
composer --version
```

Ensure Composer is functional before creating the application.

---

# 6. Verify Node

Run:

```bash
node -v
npm -v
```

The Node/npm versions should be compatible with the Laravel/Vite/Tailwind toolchain being installed.

---

# 7. Create Laravel Application

Create a fresh Laravel 13 application using the official Laravel installation workflow.

Example:

```bash
laravel new project-name
```

The exact Laravel installer options should be chosen according to the current Laravel 13 installation process.

Do not manually copy a previous project's `vendor` or `node_modules`.

---

# 8. Git Initialization

The project should use Git from the beginning.

Verify:

```bash
git status
```

Do not remove or overwrite existing Git history if working inside an existing repository.

If this is a new repository, initialize Git according to the project workflow.

---

# 9. Environment Configuration

Create the local environment from Laravel's standard environment configuration.

Verify:

```text
APP_NAME
APP_ENV
APP_KEY
APP_DEBUG
APP_URL
DB_CONNECTION
DB_DATABASE
DB_USERNAME
DB_PASSWORD
```

Never commit `.env`.

---

# 10. Application Key

Verify that Laravel has generated an application key.

If necessary:

```bash
php artisan key:generate
```

Never manually invent an application key.

---

# 11. Install Core UI Stack

Install and configure:

```text
Livewire 4
Mary UI 2
Tailwind CSS 4
daisyUI 5
```

Follow the current package documentation and project-specific package constraints.

Do not introduce:

```text
Flux
Filament
Inertia
React
Vue
```

for the base starter.

---

# 12. Install Laravel Boost

Install and configure Laravel Boost as part of the developer tooling.

Verify that Boost is available to Claude Code and compatible with the installed Laravel version.

Boost is development infrastructure.

Do not expose development-only tooling publicly.

---

# 13. Install Spatie Permission

Install:

```text
spatie/laravel-permission
```

Publish its required configuration/migrations according to the package documentation.

Run migrations only after reviewing the generated migration/configuration.

---

# 14. Configure Permission Models

The application's User model must use the appropriate Spatie Permission trait.

The implementation must follow the package's Laravel 13-compatible conventions.

Do not create a custom permission engine.

---

# 15. Permission Architecture

Establish the following conceptual structure:

```text
User
  ↓
Role
  ↓
Permission
  ↓
Policy / Gate
  ↓
Resource operation
```

Policies remain important even when permissions are used.

---

# 16. Initial Permission Seeder

Create a dedicated permission seeder.

Example conceptual permissions:

```text
users.view
users.create
users.update
users.delete

roles.view
roles.create
roles.update
roles.delete

settings.view
settings.update
```

The exact permission set may evolve.

Permissions must be idempotent.

Running the seeder multiple times must not create duplicates.

---

# 17. Super Admin

Create a supported super-admin mechanism.

The mechanism must integrate with Laravel authorization and Spatie Permission.

Avoid scattered role checks.

---

# 18. Initial Role Seeder

Create the required system role(s).

At minimum, the starter may provide:

```text
Super Admin
```

Do not populate application-specific roles.

---

# 19. First Administrator

The bootstrap should provide a safe mechanism for creating the initial administrator.

Preferred options include:

```bash
php artisan db:seed
```

or a dedicated setup command.

Do not hard-code a production administrator password.

---

# 20. Install Spatie Media Library

Install:

```text
spatie/laravel-medialibrary
```

Publish and configure its required migration/configuration.

Run the migrations.

---

# 21. Media Configuration

Configure media storage through Laravel's Filesystem.

Do not hard-code storage paths throughout the application.

The application should remain compatible with:

```text
Local
S3-compatible storage
Other Laravel Filesystem drivers
```

where supported.

---

# 22. Media Model Strategy

Do not make every model media-enabled automatically.

Models should implement Media Library functionality only when they actually own media.

---

# 23. Avatar Support

The starter may provide avatar support for users.

If implemented, use a dedicated media collection:

```text
avatar
```

Do not create unnecessary default collections.

---

# 24. Install Activity Log

Install:

```text
spatie/laravel-activitylog
```

Configure it according to the package's Laravel-compatible documentation.

---

# 25. Activity Log Strategy

Do not log every model change automatically without considering application requirements.

The starter should establish a sensible pattern for logging meaningful activity.

Examples:

```text
User created
User updated
Role changed
Important resource created
Important resource updated
Important resource deleted
```

---

# 26. Activity Log UI

Provide a reusable activity display pattern.

The starter may include:

```text
Recent Activity
```

on the dashboard.

A complete enterprise audit system is outside the scope of the starter.

---

# 27. Install Debugbar

Install Laravel Debugbar as a development dependency.

It must be configured so that it is not active in production.

Verify:

```text
APP_ENV=local
```

development behavior.

---

# 28. Testing

Configure:

```text
Pest 4
```

according to the Laravel 13-compatible setup.

The application must have a working baseline test suite before bootstrap completion.

---

# 29. Static Analysis

Configure the project's chosen static-analysis tooling.

Preferred:

```text
Larastan / PHPStan
```

Use a configuration appropriate for the installed Laravel version and PHP 8.3 minimum.

---

# 30. Code Formatting

Laravel Pint should be configured.

Verify:

```bash
vendor/bin/pint --test
```

or the project's equivalent command.

---

# 31. Database

Run:

```bash
php artisan migrate
```

Verify that all core migrations complete successfully.

Do not ignore migration errors.

---

# 32. Database Seed

Run the essential system seeders.

The seeding process should create only:

```text
System roles
System permissions
Required system settings
```

Do not create fake business data in the normal seed process.

---

# 33. Demo Data

If demo data is provided, it must have a separate seeder.

Example:

```text
DemoSeeder
```

It must be possible to avoid demo data in production.

---

# 34. Application Settings

Create the initial settings infrastructure.

At minimum, establish a convention for:

```text
Application name
Branding
Timezone
Locale
Currency
```

Do not populate unnecessary settings.

---

# 35. Branding

The starter should use configurable branding.

At minimum support:

```text
Application name
Logo
Favicon
Primary theme
```

The default may use 8bit Technologies branding.

---

# 36. Application Layout

Create the base authenticated application layout.

It should include:

```text
Sidebar
Topbar
Main content area
Notifications
User menu
Responsive mobile navigation
```

Follow `UI-DESIGN-SYSTEM.md`.

---

# 37. Authentication Layout

Authentication screens should use the same branding/theme system.

At minimum:

```text
Login
Register
Forgot Password
Reset Password
Email Verification
```

Use the selected Laravel/Livewire-compatible authentication implementation.

---

# 38. Dashboard

Create the initial dashboard.

The dashboard should demonstrate the starter rather than pretend to be a business application.

Suggested content:

```text
Welcome
System overview
Recent activity
Quick links
```

---

# 39. User Management

Implement the base user management module.

Required screens:

```text
Users
Create User
Edit User
View User
```

Required capabilities:

```text
Search
Pagination
Role assignment
Status
Authorization
```

Only add additional functionality when useful.

---

# 40. Roles Management

Implement basic role management.

Required:

```text
Role list
Create role
Edit role
Delete role where appropriate
Permission assignment
```

The interface should remain manageable when the number of permissions grows.

---

# 41. Permission Management

Provide a way for administrators to manage permissions through roles.

Do not require administrators to edit database records manually.

---

# 42. Profile

Implement the basic profile page.

Minimum:

```text
Name
Email
Password
```

Additional authentication/security functionality may be added according to the selected authentication stack.

---

# 43. Settings UI

Create a basic settings area.

Recommended:

```text
General
Appearance
Localization
```

Only include settings that are actually implemented.

---

# 44. Error Pages

Create branded error pages for commonly encountered errors:

```text
403
404
419
429
500
503
```

Follow the UI design system.

---

# 45. Health Check

Configure the application's health endpoint using Laravel's supported health-check functionality.

Verify it works locally.

Do not expose sensitive diagnostic information.

---

# 46. Storage

Run:

```bash
php artisan storage:link
```

when required by the application's local storage setup.

Verify public media handling.

---

# 47. Queue

Do not require Redis simply to bootstrap the starter.

The application should remain compatible with Laravel's default queue configuration.

If Redis is configured, verify the connection separately.

---

# 48. Scheduler

Ensure Laravel Scheduler remains available.

Do not create unnecessary scheduled jobs.

---

# 49. Mail

Configure the local mail environment appropriately.

For local development, a mail catcher or log driver may be used.

Never send real production email accidentally from local development.

---

# 50. Frontend Dependencies

Install frontend dependencies:

```bash
npm install
```

Then verify the production build:

```bash
npm run build
```

The build must complete successfully.

---

# 51. Asset Architecture

Keep frontend code simple.

Use:

```text
Tailwind
daisyUI
Mary UI
Livewire
Alpine
```

Do not introduce another frontend framework.

---

# 52. Directory Verification

After bootstrap, verify the project roughly follows:

```text
app/
├── Actions/
├── Enums/
├── Http/
├── Livewire/
├── Models/
├── Policies/
├── Services/
└── Support/

resources/
├── css/
├── js/
└── views/
    ├── components/
    │   └── 8bit/
    ├── layouts/
    └── livewire/

database/
├── factories/
├── migrations/
└── seeders/

tests/
├── Feature/
└── Unit/
```

Do not create empty directories solely to match this diagram.

---

# 53. Base Livewire Structure

Organize Livewire components by feature.

Example:

```text
app/Livewire/
├── Dashboard/
├── Users/
├── Roles/
├── Profile/
└── Settings/
```

Views should follow the same feature organization.

---

# 54. Base Components

Create only useful reusable 8bit components.

Potential components:

```text
PageHeader
EmptyState
StatusBadge
StatCard
ConfirmDialog
```

Do not create a wrapper for every Mary UI component.

---

# 55. Base Layout Components

The starter should establish:

```text
Authenticated Layout
Guest Layout
Sidebar
Topbar
Navigation
User Menu
```

These should be reusable by application pages.

---

# 56. Navigation Permissions

Administrative navigation should respect permissions.

For example:

```text
Users
Roles
Settings
```

should not be displayed to users who cannot access those sections.

Remember:

> Hiding a navigation item is not authorization.

Server-side authorization remains mandatory.

---

# 57. Base Policies

Create policies for starter-owned resources.

At minimum:

```text
User
Role
Settings
```

where appropriate.

---

# 58. Base Tests

The starter must include tests covering at least:

```text
Authentication
User authorization
Role/permission authorization
User creation
User update
User deletion
Settings authorization
```

The exact test suite may expand.

---

# 59. Permission Tests

Verify that:

```text
Unauthorized user
    ↓
Cannot access protected resource

Authorized user
    ↓
Can access allowed resource

Super Admin
    ↓
Has intended elevated access
```

---

# 60. Media Tests

Where starter media functionality exists, test:

```text
Upload
Validation
Collection behavior
Authorization
```

Do not write tests for package internals.

---

# 61. Notification Tests

Verify important Livewire actions produce the expected notification behavior.

Do not tightly couple tests to implementation details.

---

# 62. Build Verification

Before declaring bootstrap complete:

```bash
php artisan test
vendor/bin/pint --test
vendor/bin/phpstan analyse
npm run build
```

All applicable checks should pass.

---

# 63. Laravel Health Verification

Run:

```bash
php artisan about
```

Review the environment and installed components.

Check for obvious configuration problems.

---

# 64. Route Verification

Run:

```bash
php artisan route:list
```

Review the output for:

```text
Authentication
Dashboard
Users
Roles
Settings
Health
```

and verify there are no accidental debug/test routes.

---

# 65. Configuration Verification

Run:

```bash
php artisan config:show
```

where useful to inspect important configuration.

Do not expose configuration containing secrets.

---

# 66. Migration Verification

Run migrations against a clean database where practical.

The starter must be capable of being installed from scratch.

---

# 67. Fresh Installation Test

A clean bootstrap test should follow:

```text
Fresh Laravel
    ↓
Install starter dependencies
    ↓
Configure environment
    ↓
Migrate
    ↓
Seed
    ↓
Build assets
    ↓
Run tests
    ↓
Application works
```

This is one of the most important acceptance criteria.

---

# 68. Production Safety Review

Before the first release verify:

```text
[ ] APP_DEBUG disabled for production
[ ] No secrets committed
[ ] Debugbar development-only
[ ] Development credentials not exposed
[ ] Health endpoint reviewed
[ ] Storage configuration reviewed
[ ] Mail configuration reviewed
[ ] Queue configuration reviewed
[ ] Error pages do not expose internals
```

---

# 69. Documentation Installation

The repository should contain:

```text
CLAUDE.md
PACKAGES.md
ARCHITECTURE.md
CONVENTIONS.md
UI-DESIGN-SYSTEM.md
FEATURE-SET.md
PROJECT-BOOTSTRAP.md
```

These documents should describe the same architecture.

Avoid contradictory documentation.

---

# 70. README

Create a concise project README.

It should explain:

```text
What the starter is
Technology stack
Requirements
Installation
Development commands
Testing
Architecture documentation
```

The README should point developers to the detailed documentation rather than duplicating it.

---

# 71. Development Commands

Document the primary commands.

Example:

```bash
php artisan serve
npm run dev
php artisan test
vendor/bin/pint
vendor/bin/phpstan analyse
npm run build
```

Use the actual project configuration.

---

# 72. First Run Experience

A developer should be able to understand the project after reading the README and documentation without needing to inspect every source file.

---

# 73. Git Ignore

Verify `.gitignore` excludes:

```text
.env
/vendor
/node_modules
```

and other standard generated/runtime files.

---

# 74. Git Baseline

Before the first starter commit:

```bash
git status
```

Review every changed file.

Do not commit:

```text
Secrets
Temporary files
Debug output
IDE-specific files
Generated runtime data
```

unless intentionally required.

---

# 75. First Commit

The first commit should represent a clean, working starter baseline.

Suggested commit:

```text
chore: initialize 8bit laravel starter v1
```

---

# 76. Tag

After the starter passes all verification:

```text
v1.0.0
```

may be created as the initial stable starter release.

Only tag after the project is genuinely usable.

---

# 77. Bootstrap Acceptance Criteria

The bootstrap is complete only when:

```text
[ ] PHP 8.3+ verified
[ ] Laravel 13 installed
[ ] Livewire configured
[ ] Mary UI configured
[ ] Tailwind configured
[ ] daisyUI configured
[ ] Laravel Boost installed
[ ] Spatie Permission installed
[ ] Spatie Media Library installed
[ ] Spatie Activitylog installed
[ ] Debugbar development-only
[ ] Pest configured
[ ] Pint configured
[ ] Static analysis configured
[ ] Authentication works
[ ] User management works
[ ] Roles/permissions work
[ ] Profile works
[ ] Settings work
[ ] Dashboard works
[ ] Media upload works where implemented
[ ] Notifications work
[ ] Error pages work
[ ] Health check works
[ ] Database migrations work
[ ] Seeders work
[ ] Frontend build works
[ ] Tests pass
[ ] Static analysis passes
[ ] Formatting passes
[ ] Documentation exists
[ ] Git baseline is clean
```

---

# 78. What Claude Code Must NOT Do

During bootstrap, Claude Code must not:

- Install Flux.
- Install Livewire Alert.
- Install Filament.
- Install React.
- Install Vue.
- Install Inertia.
- Install payment packages.
- Install social media packages.
- Install SEO packages.
- Install search engines.
- Install multi-tenancy packages.
- Install unnecessary UI libraries.
- Create speculative modules.
- Create fake business functionality.
- Add unnecessary repositories/interfaces.
- Modify unrelated Laravel defaults.
- Skip verification because installation "looks correct."

---

# 79. Handling Package Compatibility Problems

If a required package does not support:

```text
PHP 8.3
```

or:

```text
Laravel 13
```

do not silently downgrade or replace the starter architecture.

Stop and evaluate:

1. Compatible package version.
2. Alternative supported package.
3. Whether the functionality can be implemented natively.
4. Whether the starter requirements need revision.

Document the decision.

---

# 80. Handling Version Drift

Package versions will change over time.

Do not blindly copy old installation commands from this document if they conflict with current official package documentation.

The architectural requirement is more important than an outdated command.

---

# 81. Bootstrap Idempotency

Where practical, setup operations should be safe to repeat.

For example:

```text
Migration
Seeder
Configuration
Storage setup
Permission creation
```

should not produce duplicate records or corrupt configuration.

---

# 82. Bootstrap Failure

If a bootstrap step fails:

1. Identify the actual error.
2. Determine whether it is environmental, dependency-related, or code-related.
3. Fix the root cause.
4. Re-run the failed step.
5. Re-run dependent verification.

Do not simply suppress the error.

---

# 83. Final Claude Code Workflow

Claude Code should follow:

```text
READ
  ↓
PACKAGES.md
ARCHITECTURE.md
CONVENTIONS.md
UI-DESIGN-SYSTEM.md
FEATURE-SET.md
CLAUDE.md

INSPECT
  ↓
Laravel environment
PHP version
Composer
Node
Git

BOOTSTRAP
  ↓
Laravel
Livewire
Mary UI
Tailwind
daisyUI
Boost
Spatie packages
Developer tooling

CONFIGURE
  ↓
Authentication
Permissions
Media
Activity
Settings
Branding
UI shell

BUILD
  ↓
Dashboard
Users
Roles
Profile
Settings
Error pages

VERIFY
  ↓
Migrations
Seeders
Tests
Pint
PHPStan
Frontend build
Routes
Health

REVIEW
  ↓
Security
Git diff
Documentation

RELEASE
  ↓
Commit
Tag v1.0.0
```

---

# 84. Final Principle

The bootstrap process must produce a starter that is:

```text
Fresh
Clean
Predictable
Documented
Secure
Tested
Maintainable
PHP 8.3 compatible
Laravel 13 compatible
Claude Code friendly
```

The end result should feel like a **proper Laravel application**, not a collection of packages glued together.

> Bootstrap once. Build many products.