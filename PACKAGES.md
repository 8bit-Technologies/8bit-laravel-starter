# 8bit Laravel Starter v1
## Package Specification

**Organization:** 8bit Technologies  
**Starter:** 8bit Laravel Starter  
**Version:** 1.0  
**Minimum PHP:** 8.3  
**Primary Framework:** Laravel 13  
**Primary UI:** Livewire 4

---

## 1. Package Philosophy

The 8bit Laravel Starter is an opinionated foundation for building Laravel business applications.

Packages must satisfy at least one of these criteria:

1. Solve a recurring requirement across multiple 8bit projects.
2. Significantly improve developer productivity.
3. Establish a reusable engineering standard.
4. Provide infrastructure that would otherwise be repeatedly rebuilt.
5. Improve security, maintainability, testing, or observability.

Avoid adding packages merely because they are popular.

Every third-party dependency should have a clear purpose.

---

# 2. Platform Requirements

| Component | Requirement |
|---|---|
| PHP | `^8.3` |
| Laravel | `^13.0` |
| Database | MySQL 8+ / compatible supported database |
| Node.js | Current LTS |
| Package manager | npm |
| Build system | Vite |

PHP 8.3 is the minimum supported version. The application must not use PHP language features requiring PHP 8.4+ unless the minimum version is deliberately changed.

Laravel 13 itself requires PHP 8.3+. 

---

# 3. Core Framework

## Laravel

**Package:** `laravel/framework`

**Version:** `^13.0`

Laravel is the foundation of every 8bit application.

Do not replace Laravel-native functionality with third-party packages unless there is a strong reason.

---

# 4. Frontend Foundation

## Livewire

**Package:** `livewire/livewire`

**Version:** `^4.0`

Livewire is the primary application UI framework.

Use Livewire for:

- CRUD interfaces
- Forms
- Tables
- Filtering
- Search
- Pagination
- Modals
- Drawers
- Interactive dashboards
- Upload interfaces
- Dynamic application workflows

Livewire 4 supports Laravel 10+ and PHP 8.1+. It also bundles Alpine.js. 

### Rule

Do not introduce React, Vue, Inertia, or another frontend framework into the starter unless a specific project has an explicit architectural requirement.

---

## Alpine.js

Alpine is provided through Livewire.

Do not manually add Alpine as a separate dependency unless a project has a specific reason to do so.

Use Alpine only for small client-side interactions that do not justify a separate JavaScript architecture.

---

## Tailwind CSS

**Version:** Tailwind CSS 4

Tailwind is the primary styling system.

Avoid custom CSS unless:

- Tailwind cannot reasonably accomplish the requirement; or
- a reusable design-system rule requires it.

---

## daisyUI

**Version:** daisyUI 5

daisyUI provides the underlying styling/component primitives used by Mary UI.

Application code should prefer 8bit UI conventions over directly depending heavily on daisyUI implementation details.

---

## Mary UI

**Package:** `robsontenorio/mary`

**Version:** `^2.0`

Mary UI is the primary UI component library.

Use it for:

- Buttons
- Inputs
- Selects
- Modals
- Drawers
- Dropdowns
- Alerts
- Toasts
- Tables
- Badges
- Tabs
- Pagination
- Form components
- File upload interfaces

Mary UI 2 is designed for the Tailwind 4 / daisyUI 5 ecosystem and supports Livewire 4. 

### Important architectural rule

Mary UI is an implementation dependency, not the application's permanent UI API.

Where useful, 8bit-specific wrapper components may be created.

Example:

```blade
<x-8bit.button>
    Save
</x-8bit.button>
```

rather than exposing third-party implementation details throughout every application.

---

# 5. AI-Assisted Development

## Laravel Boost

**Package:** `laravel/boost`

**Dependency:** Development only

Install using:

```bash
composer require laravel/boost --dev
```

Initialize using:

```bash
php artisan boost:install
```

Laravel Boost provides Laravel-specific AI guidelines, agent skills, MCP tools, and access to Laravel/package documentation. It explicitly supports Claude Code.

### 8bit requirement

Boost must be configured together with the 8bit-specific Claude Code rules.

Boost provides the Laravel knowledge layer.

`CLAUDE.md` provides the 8bit Technologies project rules.

These should complement each other rather than compete.

---

# 6. Authorization

## Spatie Laravel Permission

**Package:** `spatie/laravel-permission`

**Major version:** `8.x`

Use for:

- Roles
- Permissions
- Authorization
- Gate integration
- Permission-aware navigation

Laravel 12/13 are supported by the current v8 line with PHP 8.3+.

### Standard permission naming

Use:

```text
resource.action
```

Examples:

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

Do not create a second custom permission system.

---

# 7. Media

## Spatie Laravel Media Library

**Package:** `spatie/laravel-medialibrary`

**Major version:** `11.x`

Use for:

- Images
- Documents
- Attachments
- Avatars
- Logos
- Gallery images
- Generated files

The current v11 package requires PHP 8.2+ and Laravel 10+, so it fits the PHP 8.3 / Laravel 13 foundation.

### Standard base collections

```text
avatar
logo
documents
attachments
```

Business applications may add domain-specific collections.

Examples:

```text
room-images
hotel-gallery
event-banner
product-images
customer-documents
```

Do not build an independent media/file storage abstraction that duplicates Media Library.

---

# 8. Audit Trail

### Spatie Laravel Activitylog

**Package:** `spatie/laravel-activitylog`

**Status:** Core

**Version:** `^4.12`

**Reason:** Provides application activity/history logging.

**PHP compatibility:** Supports the starter's PHP 8.3+ requirement.

**Important:** The starter intentionally uses the v4 major version. Do not upgrade to v5 because v5 requires PHP 8.4+.

### Requirement

The starter should still provide a standardized audit-log architecture.

---

# 9. Development Debugging

## Laravel Debugbar

**Package:** `fruitcake/laravel-debugbar`

**Dependency:** Development only

Install:

```bash
composer require fruitcake/laravel-debugbar --dev
```

Debugbar is automatically disabled in production/testing environments by its normal configuration when appropriate.

Use for:

- Queries
- Request timing
- Memory
- Events
- Exceptions
- View information

Never rely on Debugbar in production.

---

# 10. Laravel Telescope

**Package:** `laravel/telescope`

**Dependency:** Development / staging

Use Telescope for deeper Laravel application inspection.

Primary use cases:

- Requests
- Jobs
- Commands
- Exceptions
- Notifications
- Mail
- Queries
- Cache
- Logs

Telescope should not automatically become a publicly accessible production administration feature.

---

# 11. Testing

## Pest

**Package:** `pestphp/pest`

**Version:** `^4.0`

Pest 4 requires PHP 8.3+, making it the correct choice for the 8bit PHP 8.3 baseline. Pest 5 requires PHP 8.4+, so it is explicitly excluded from v1.

Use Pest for:

- Feature tests
- Unit tests
- Authorization tests
- Livewire tests
- API tests
- Critical business workflows

Prefer feature tests for application behavior.

---

# 12. Code Formatting

## Laravel Pint

Use Laravel Pint as the standard PHP formatter.

Every project must be capable of running:

```bash
vendor/bin/pint
```

CI should verify formatting.

---

# 13. Static Analysis

## Larastan / PHPStan

Use Larastan with PHPStan.

Purpose:

- Detect incorrect types
- Detect unreachable code
- Detect invalid method calls
- Improve maintainability
- Catch errors before runtime

The exact supported version will be selected during Composer installation based on the Laravel 13 / PHP 8.3 compatibility matrix.

---

# 14. API Authentication

## Laravel Sanctum

**Status:** Optional module

Do not force Sanctum into applications that never expose an API.

Install when the project requires:

- Mobile applications
- External APIs
- SPA authentication
- API tokens
- Third-party integrations

Standard API prefix:

```text
/api/v1
```

---

# 15. Queues

Use Laravel's native queue system.

### Default

```env
QUEUE_CONNECTION=database
```

This keeps the starter deployable without Redis.

Applications that require high-volume asynchronous processing may enable Redis.

---

# 16. Redis

**Status:** Optional infrastructure

Redis should not be a mandatory requirement for the base starter.

Possible uses:

- Cache
- Queues
- Rate limiting
- Locks
- Sessions
- High-volume application workloads

---

# 17. Laravel Horizon

**Status:** Optional infrastructure

Install when Redis queues are used heavily.

Do not include Horizon in the mandatory base installation.

---

# 18. Laravel Scout

**Status:** Optional

Use when an application requires advanced search.

Possible engines:

- Meilisearch
- Typesense
- Algolia
- Other supported search engines

Do not add Scout merely because a project has a basic search box.

---

# 19. Laravel Excel

**Status:** Optional

Use for:

- Excel import
- Excel export
- CSV import/export
- Large spreadsheet processing

This will be especially useful for ERP, factory, inventory, accounting, and reporting applications.

---

# 20. Spatie Laravel Backup

**Status:** Optional but strongly recommended for production applications**

Use for automated:

- Database backups
- Application backups
- Media/storage backups

Production applications should have a documented backup strategy.

---

# 21. Spatie Query Builder

**Status:** Optional**

Useful primarily for API/query-heavy applications.

Do not force it into every Livewire CRUD.

---

# 22. Spatie Laravel Data

**Status:** Optional**

Use where DTO/data-object architecture provides meaningful value.

Do not create Data classes for trivial forms or models merely for architectural purity.

---

# 23. Explicitly Excluded

The following packages/technologies are not part of the 8bit Starter v1 core:

```text
Flux UI
Livewire Alert
React
Vue
Inertia
Filament
Another permission package
Another media library
Another notification package
Generic repository package
Generic service-layer package
Mandatory Redis
Mandatory Elasticsearch
Mandatory search engine
Mandatory multi-tenancy
```

### Flux

Excluded because the free component set is too restrictive for the intended 8bit application foundation.

### Livewire Alert

Excluded because notifications can be standardized using Laravel/Livewire + Mary UI without adding another dependency.

---

# 24. Notification Architecture

The starter will provide a unified 8bit notification convention.

Supported categories:

```text
success
error
warning
info
```

Notifications should support:

- Livewire actions
- Session flash messages
- Browser-side toast notifications
- Persistent application notifications

The implementation should be hidden behind an 8bit convention rather than tightly coupling business code to Mary UI.

Example desired application-level API:

```php
$this->notifySuccess('Customer created successfully.');
```

The exact API will be finalized in `CONVENTIONS.md`.

---

# 25. Package Installation Policy

Before adding a package:

1. Confirm that Laravel does not already provide the functionality.
2. Confirm PHP 8.3 compatibility.
3. Confirm Laravel 13 compatibility.
4. Check package maintenance status.
5. Check licensing.
6. Check whether an existing 8bit package already solves the problem.
7. Document the reason for adding the package.

Claude Code must not introduce a new Composer dependency without considering these rules.

---

# 26. Version Policy

Do not blindly use `dev-main`, `dev-master`, or unbounded package versions.

Prefer stable major-version constraints compatible with:

```text
PHP 8.3+
Laravel 13
```

Major package upgrades should be intentional.

Minor and patch updates may be handled through normal dependency maintenance.

---

# 27. Compatibility Matrix

The v1 baseline is:

```text
PHP              8.3+
Laravel          13.x
Livewire         4.x
Mary UI          2.x
Tailwind         4.x
daisyUI          5.x
Pest             4.x
Spatie Permission 8.x
Spatie Media      11.x
Laravel Boost     current Laravel 13 compatible release
```

Activitylog requires a special compatibility decision because its current v5 release requires PHP 8.4+.

---

# 28. CI Compatibility Requirement

CI must test at minimum:

```text
PHP 8.3
Latest supported PHP
```

The purpose is to prevent code from accidentally becoming incompatible with the 8bit PHP 8.3 minimum.

---

# 29. Core vs Optional

### Core

```text
Laravel
Livewire
Tailwind
Mary UI
daisyUI
Laravel Boost
Spatie Permission
Spatie Media Library
Spatie Activitylog
Pest
Pint
PHPStan/Larastan
```

Spatie Activitylog is Core, pinned to `^4.12`. The starter's minimum PHP version is 8.3, and Activitylog v5 requires PHP 8.4+, so v4 is the required major version until the starter's PHP floor is deliberately raised.

### Development

```text
Debugbar
Telescope
```

### Conditional / Optional

```text
Sanctum
Redis
Horizon
Scout
Laravel Excel
Spatie Backup
Spatie Query Builder
Spatie Laravel Data
```

---

# 30. Final Rule

The 8bit Laravel Starter should provide a strong foundation without becoming a framework inside Laravel.

Prefer:

```text
Laravel conventions
+
small reusable 8bit conventions
+
well-maintained packages
```

over:

```text
Laravel
+
large abstraction layer
+
generic repositories
+
generic services
+
generic factories
+
custom ORM
+
custom permission system
+
custom UI framework
```

The starter should make developers faster, not make Laravel harder to understand.