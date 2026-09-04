# 8bit Laravel Starter v1

## Claude Code Project Instructions

**Organization:** 8bit Technologies  
**Starter:** 8bit Laravel Starter  
**Minimum PHP:** 8.3  
**Laravel:** 13  
**Livewire:** 4  
**UI:** Mary UI 2 + Tailwind CSS 4 + daisyUI 5

---

# 1. Mission

You are working on the **8bit Laravel Starter**, a reusable Laravel foundation maintained by **8bit Technologies**.

This starter will be used as the starting point for multiple commercial Laravel applications.

Your primary goals are:

1. Keep the architecture simple.
2. Follow Laravel conventions.
3. Produce maintainable code.
4. Avoid unnecessary dependencies.
5. Build reusable patterns where they provide real value.
6. Preserve PHP 8.3 compatibility.
7. Maintain a consistent 8bit UI/UX.
8. Write testable code.
9. Make decisions that remain maintainable across future Laravel upgrades.

---

# 2. Mandatory Reading

Before making architectural or substantial code changes, read:

```text
PACKAGES.md
ARCHITECTURE.md
CONVENTIONS.md
```

These documents define:

- Installed packages
- Architecture
- Naming conventions
- UI conventions
- Feature organization
- Testing expectations
- Dependency rules

When these documents conflict with an existing implementation, inspect the codebase and determine whether the implementation is intentional before changing it.

---

# 3. Core Technology Rules

The starter is based on:

```text
PHP 8.3+
Laravel 13
Livewire 4
Mary UI 2
Tailwind CSS 4
daisyUI 5
Laravel Boost
Spatie Permission
Spatie Media Library
Pest 4
Laravel Pint
Larastan/PHPStan
```

Do not introduce Flux.

Do not introduce Livewire Alert.

Do not introduce React, Vue, Inertia, Filament, or another application UI framework.

---

# 4. PHP Compatibility

PHP **8.3 is the minimum supported version**.

This is a hard requirement.

Do not use:

- PHP 8.4-only language features
- PHP 8.5-only language features
- Packages requiring PHP >8.3

unless the project owner explicitly changes the minimum PHP version.

When adding a dependency, verify its PHP 8.3 compatibility.

---

# 5. Laravel First

Before implementing custom functionality:

> Check whether Laravel already provides the required functionality.

Prefer:

```text
Laravel native functionality
```

over:

```text
custom abstraction
```

Examples:

- Laravel Policies instead of custom authorization classes
- Laravel Validation instead of custom validators
- Laravel Events instead of custom event buses
- Laravel Jobs instead of custom queue abstractions
- Laravel Notifications instead of custom notification infrastructure
- Eloquent relationships instead of custom repository layers

---

# 6. Do Not Build a Second Framework

Never create abstractions merely because they appear architecturally sophisticated.

Avoid automatically creating:

```text
Repository
RepositoryInterface
Service
ServiceInterface
Manager
ManagerInterface
Factory
FactoryInterface
DTO
DTOInterface
```

for every feature.

Only introduce these when there is a concrete reason.

---

# 7. Before Changing Code

Before modifying an existing feature:

1. Inspect relevant files.
2. Understand current behavior.
3. Identify existing conventions.
4. Check related models/components/actions/policies.
5. Check existing tests.
6. Determine the smallest appropriate change.

Do not start coding based solely on assumptions.

---

# 8. Search Before Creating

Before creating a new:

- Component
- Action
- Service
- Helper
- Trait
- Model method
- UI pattern
- Notification
- Permission

search the project for an existing equivalent.

Reuse existing infrastructure whenever possible.

---

# 9. No Unnecessary Refactoring

When implementing a requested feature:

> Do not refactor unrelated code.

Do not:

- Rename unrelated classes.
- Reformat unrelated files.
- Upgrade packages.
- Replace libraries.
- Restructure the application.
- Rewrite working components.

unless explicitly requested or required to complete the task.

---

# 10. Dependency Policy

Never install a package simply because it is convenient.

Before adding a dependency:

1. Check Laravel's native capabilities.
2. Check installed packages.
3. Check 8bit conventions.
4. Check PHP 8.3 compatibility.
5. Check Laravel 13 compatibility.
6. Check package maintenance.
7. Check licensing.
8. Determine whether the dependency will be useful across multiple projects.
9. Document the reason.

If the functionality is small, prefer a local implementation.

---

# 11. UI Stack

The application UI stack is:

```text
Livewire
    ↓
8bit UI conventions
    ↓
Mary UI
    ↓
Tailwind CSS / daisyUI
```

Mary UI is the primary component library.

Do not introduce another UI library.

---

# 12. Livewire

Livewire is the primary interactive application UI framework.

Use Livewire for:

- CRUD
- Forms
- Tables
- Filters
- Search
- Pagination
- Dashboards
- Interactive workflows
- Modals
- Drawers
- Upload interfaces

Do not introduce another frontend framework for ordinary application functionality.

---

# 13. Alpine

Livewire provides Alpine.

Use Alpine for lightweight client-side behavior where appropriate.

Do not create a separate frontend architecture around Alpine.

---

# 14. Mary UI

Use Mary UI for common UI components.

Before creating a custom component, check whether Mary UI already provides the required functionality.

Use 8bit components for repeated application-specific patterns.

Do not create wrappers around every Mary UI component.

---

# 15. 8bit UI

Reusable 8bit UI components belong under:

```text
resources/views/components/8bit/
```

Examples:

```text
page-header
empty-state
status-badge
stat-card
permission-gate
```

Create a component when a pattern:

- Appears repeatedly.
- Has meaningful application-level behavior.
- Needs consistent styling.
- Benefits from centralized maintenance.

---

# 16. UI Consistency

Do not invent a new design pattern for each page.

Reuse:

- Page headers
- Buttons
- Forms
- Tables
- Status badges
- Empty states
- Modals
- Notifications
- Loading states

The application should visually feel like an 8bit Technologies product.

---

# 17. Responsive Design

Every UI implementation must consider:

```text
Mobile
Tablet
Desktop
Large desktop
```

Do not assume desktop-only usage.

Tables, forms, navigation and dialogs must remain usable on smaller screens.

---

# 18. Accessibility

Every UI feature should consider:

- Labels
- Keyboard navigation
- Focus states
- Accessible button text
- Form error association
- Appropriate ARIA attributes
- Contrast
- Screen-reader usability

Do not treat accessibility as optional polish.

---

# 19. Livewire Component Structure

Organize components by feature.

Example:

```text
app/Livewire/
└── Customers/
    ├── Index.php
    ├── Create.php
    ├── Edit.php
    └── Show.php
```

Views:

```text
resources/views/livewire/customers/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php
```

---

# 20. Livewire Responsibilities

Livewire components should handle:

- UI state
- Validation
- Pagination
- Filtering
- User interaction
- Authorization calls
- Calling Actions

Do not put large business workflows inside Livewire components.

---

# 21. Business Logic

Use the following decision system:

```text
UI state
    → Livewire

Validation
    → Form / Form Request

Authorization
    → Policy / Permission

Meaningful business operation
    → Action

Reusable infrastructure / external integration
    → Service

Long-running operation
    → Job

Something happened
    → Event

React to an event
    → Listener

Persistent data
    → Model

Finite state
    → Enum
```

---

# 22. Actions

Use Actions for meaningful application operations.

Examples:

```text
CreateCustomer
ConfirmBooking
CancelBooking
GenerateInvoice
ProcessPayment
PublishSocialPost
```

Do not create trivial Actions merely to wrap Eloquent.

---

# 23. Services

Use Services for reusable infrastructure and integrations.

Good examples:

```text
PaymentGatewayService
WhatsAppService
SmsService
PdfService
SocialMediaService
SeoService
```

Avoid generic model services unless there is genuine reusable logic.

---

# 24. Jobs

Use Jobs for:

- Long-running work
- External API processing
- Imports
- Exports
- Reports
- Media processing
- Social publishing
- Large background operations

Jobs should be retry-safe where practical.

---

# 25. Events

Events represent facts that occurred.

Good:

```text
BookingConfirmed
InvoicePaid
CustomerRegistered
```

Do not name events as commands.

---

# 26. Authorization

Authorization is mandatory for protected operations.

Use:

```php
$this->authorize(...)
```

or:

```php
Gate::authorize(...)
```

Do not rely on UI visibility as security.

---

# 27. Permissions

Use Spatie Permission.

Permission naming:

```text
resource.action
```

Examples:

```text
customers.view
customers.create
customers.update
customers.delete

bookings.confirm
bookings.cancel
```

Do not introduce another permission system.

---

# 28. Role Checks

Prefer permissions and policies over direct role checks.

Avoid:

```php
if ($user->role === 'admin')
```

Prefer:

```php
$user->can('customers.delete')
```

or appropriate policy authorization.

---

# 29. CRUD Pattern

Standard CRUD:

```text
Index
Create
Edit
Show
```

Delete should generally be an action rather than a dedicated page.

A normal CRUD should consider:

```text
Search
Filter
Sort
Pagination
Validation
Authorization
Empty state
Loading state
Delete confirmation
```

Only implement features that make sense for the resource.

---

# 30. Forms

Use simple Livewire properties for simple forms.

Use Livewire Form Objects when:

- The form is large.
- Create/Edit share substantial state.
- Validation is complex.
- The form is reused.

Do not create Form Objects for trivial forms.

---

# 31. Validation

Never trust client input.

Validate all user-provided data.

Validation must happen before business operations.

Use Laravel validation rules.

---

# 32. Database

Prefer Eloquent.

Use Query Builder where appropriate.

Use raw SQL only when there is a clear reason.

Always consider:

- Indexes
- N+1 queries
- Pagination
- Eager loading
- Query size

---

# 33. N+1 Queries

Actively check for N+1 problems.

Use eager loading when relationships are needed.

Example:

```php
Customer::with('bookings')->paginate(25);
```

Do not blindly eager-load everything.

---

# 34. Transactions

Use database transactions when multiple database operations must succeed or fail together.

Do not use transactions around every operation without reason.

---

# 35. Models

Models may contain:

- Relationships
- Casts
- Scopes
- Accessors
- Mutators
- Small domain methods

Large workflows belong in Actions.

---

# 36. Enums

Use PHP enums for finite state values.

Examples:

```text
BookingStatus
PaymentStatus
OrderStatus
UserStatus
```

Avoid magic strings throughout the codebase.

---

# 37. Media

Use Spatie Media Library for managed application media.

Do not build another media abstraction.

Validate uploads for:

- Type
- Size
- Dimensions where applicable
- Security

---

# 38. Settings

Use the centralized 8bit Settings convention.

Do not repeatedly query settings directly from business code.

Preferred conceptual API:

```php
setting('company.name');
```

The exact implementation must follow the project's established Settings infrastructure.

---

# 39. Notifications

Do not install Livewire Alert.

Use the 8bit notification convention.

Preferred API:

```php
$this->notifySuccess('Customer created successfully.');
$this->notifyError('Unable to create customer.');
$this->notifyWarning('This customer requires attention.');
$this->notifyInfo('Report generation has started.');
```

Keep notification implementation independent of business logic.

---

# 40. Confirmation Dialogs

Use a consistent confirmation pattern for destructive operations.

Flow:

```text
User action
    ↓
Confirmation
    ↓
Authorization
    ↓
Action
    ↓
Notification
    ↓
UI refresh
```

---

# 41. File Uploads

All uploads must be validated.

Never trust:

- Filename
- Extension
- MIME information supplied by the browser
- Client-side validation alone

Use server-side validation.

---

# 42. Security

Every feature must consider:

```text
Authentication
Authorization
Validation
CSRF
Mass assignment
File upload security
XSS
SQL injection
Rate limiting
Sensitive data exposure
```

Never weaken security to simplify development.

---

# 43. Secrets

Never commit:

- API keys
- Passwords
- Tokens
- Private keys
- Production credentials

Use environment variables.

Never print secrets in logs.

---

# 44. Configuration

Do not use:

```php
env('SOME_VALUE')
```

throughout application code.

Use configuration:

```php
config('services.some_service.key')
```

Environment variables belong in configuration files.

---

# 45. Tests

Every meaningful feature should have tests.

Typical CRUD coverage:

```text
Create
Update
Delete
Validation
Authorization
```

Business-critical features should include failure scenarios.

---

# 46. Pest

Use Pest 4.

Prefer behavior-oriented test descriptions.

Good:

```php
it('allows an authorized user to create a customer', function () {
    // ...
});
```

Avoid vague test names.

---

# 47. Factories

Major models should generally have factories.

Use factory states for common scenarios.

Example:

```php
Booking::factory()->confirmed()
```

Factories should generate realistic development/test data.

---

# 48. Seeders

Role and permission seeders should be idempotent.

Running seeders repeatedly should not create duplicates.

Demo data should remain separate from essential system data.

---

# 49. Code Quality

Use:

```text
Laravel Pint
Larastan/PHPStan
Pest
```

Code should be formatted and statically analyzed.

---

# 50. Verification

After meaningful changes, run the relevant checks.

Typical:

```bash
php artisan test
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

Use the actual configured static-analysis command if different.

Do not claim tests passed without actually running them.

---

# 51. Build Verification

When frontend changes are made, run the project's appropriate build/check commands.

For example:

```bash
npm run build
```

Do not assume frontend changes are valid merely because the Blade code looks correct.

---

# 52. Claude Code Package Installation

Before installing a package, explicitly determine:

```text
Why is it needed?
Can Laravel solve this?
Can an installed package solve this?
Does it support PHP 8.3?
Does it support Laravel 13?
Is it maintained?
Is its license appropriate?
Will multiple 8bit projects benefit?
```

If the answer is unclear, do not install it without discussing the trade-off.

---

# 53. Claude Code Upgrade Policy

Do not upgrade packages simply because newer versions exist.

Package upgrades should be deliberate.

Before upgrading:

1. Check compatibility.
2. Read relevant upgrade documentation.
3. Check breaking changes.
4. Run tests.
5. Run static analysis.
6. Verify frontend/build behavior.

---

# 54. Laravel Upgrades

When upgrading Laravel:

1. Review Laravel upgrade documentation.
2. Review dependency compatibility.
3. Upgrade dependencies deliberately.
4. Run tests.
5. Run static analysis.
6. Run frontend builds.
7. Review deprecated APIs.
8. Review application behavior.

Never perform a major Laravel upgrade casually during an unrelated feature.

---

# 55. Git

Create focused commits.

Preferred:

```text
feat: add customer management
fix: prevent duplicate bookings
refactor: extract invoice generation action
test: cover customer authorization
```

Avoid:

```text
update
changes
stuff
final
```

---

# 56. Git Safety

Do not:

- Force-push
- Reset unrelated work
- Delete branches
- Rewrite history

unless explicitly instructed.

Preserve existing user work.

Before destructive Git operations, verify exactly what will be affected.

---

# 57. Existing User Changes

If the working tree contains changes you did not create:

> Do not overwrite them.

Inspect them first.

Separate your changes from existing work.

---

# 58. Database Migration Safety

Before modifying a migration:

Determine whether the migration has already been used in a real environment.

Do not casually rewrite historical migrations that may already have been deployed.

For established projects, create a new migration instead.

---

# 59. Production Safety

Never perform destructive production operations without explicit authorization.

Be especially careful with:

```text
Database
Storage
Queues
Cache
Migrations
User data
Permissions
```

---

# 60. Error Handling

Do not hide errors simply to make the UI appear successful.

Handle expected failures gracefully.

Unexpected failures should remain observable through Laravel's exception/logging systems.

---

# 61. Performance

Avoid obvious performance problems.

Watch for:

- N+1 queries
- Large unpaginated lists
- Large synchronous imports
- Repeated expensive queries
- Excessive API calls
- Missing indexes

Do not prematurely optimize without evidence.

---

# 62. Documentation

Update documentation when introducing:

- New architecture
- New package
- New convention
- New integration
- Important configuration
- Non-obvious behavior

Do not create documentation for trivial implementation details.

---

# 63. Feature Implementation Workflow

When asked to build a feature:

### Step 1 — Understand

Restate internally what the feature needs to accomplish.

### Step 2 — Inspect

Search the existing application for related:

```text
Models
Policies
Permissions
Actions
Services
Components
Views
Tests
Routes
```

### Step 3 — Plan

Determine:

```text
Database changes?
Model?
Policy?
Permissions?
Action?
Service?
Job?
Livewire component?
UI components?
Tests?
```

### Step 4 — Implement

Make the smallest appropriate change.

### Step 5 — Test

Add/update tests.

### Step 6 — Verify

Run relevant quality checks.

### Step 7 — Review

Inspect the final diff for:

- Unnecessary changes
- Security issues
- Duplicate code
- Naming inconsistencies
- Missing authorization
- Missing validation
- Missing tests

---

# 64. Simple Feature Rule

Not every feature needs every layer.

For example:

```text
Simple CRUD
    ↓
Model
Policy
Livewire
Tests
```

A complex feature might require:

```text
Livewire
    ↓
Action
    ↓
Service
    ↓
Job
    ↓
External API
```

Do not add layers simply to make the architecture look complete.

---

# 65. New Feature Checklist

Before considering a feature complete, check:

```text
[ ] Requirement understood
[ ] Existing code inspected
[ ] Existing patterns reused
[ ] Database designed correctly
[ ] Validation implemented
[ ] Authorization implemented
[ ] Permissions added where required
[ ] UI follows 8bit conventions
[ ] Responsive behavior considered
[ ] Accessibility considered
[ ] Loading states considered
[ ] Empty states considered
[ ] Error states considered
[ ] Tests added
[ ] Pint passes
[ ] Static analysis passes
[ ] Frontend build passes when applicable
[ ] Final diff reviewed
```

---

# 66. When to Ask the User

Ask for clarification when:

- The requirement is genuinely ambiguous.
- Two interpretations would produce materially different behavior.
- A destructive operation is requested without sufficient detail.
- A business rule is missing and cannot safely be inferred.
- A package choice materially affects architecture.
- A UI decision has significant product implications.

Do not ask unnecessary questions when the intent is clear and Laravel conventions provide a sensible implementation.

---

# 67. Do Not Over-Engineer

The following are warning signs:

```text
One CRUD feature
    ↓
12 classes
    ↓
4 interfaces
    ↓
3 repositories
    ↓
2 factories
```

If the feature can be implemented clearly with fewer moving parts, do so.

---

# 68. Reusability Rule

Build reusable infrastructure when:

```text
The pattern is repeated
AND
The abstraction genuinely reduces duplication
AND
The abstraction remains understandable
```

Do not predict every future requirement.

---

# 69. 8bit Starter Principle

The starter should evolve based on real projects.

When the same problem appears repeatedly across multiple 8bit applications:

```text
Project A
    ↓
Project B
    ↓
Project C
    ↓
Repeated pattern identified
    ↓
Evaluate for starter inclusion
```

Do not add speculative infrastructure.

---

# 70. Final Decision Hierarchy

When choosing an implementation:

```text
1. Laravel native functionality
2. Existing 8bit convention
3. Existing installed package
4. Small custom implementation
5. New dependency
```

Choose the lowest level that solves the problem well.

---

# 71. Final Principle

The 8bit Laravel Starter should make development faster without hiding Laravel.

The desired developer experience is:

```text
Easy to understand
        +
Easy to extend
        +
Easy to test
        +
Easy for Claude Code to maintain
        +
Easy to upgrade
```

The application should still feel like a normal Laravel application.

---

# 72. Claude Code Final Instruction

When uncertain:

> Inspect first. Reuse existing patterns. Prefer Laravel. Make the smallest correct change. Protect PHP 8.3 compatibility. Test the behavior. Do not over-engineer.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.3. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

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

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

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

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

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

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

</laravel-boost-guidelines>
