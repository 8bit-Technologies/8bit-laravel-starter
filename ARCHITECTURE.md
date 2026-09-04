# 8bit Laravel Starter v1
## Architecture Specification

**Organization:** 8bit Technologies  
**Starter:** 8bit Laravel Starter  
**Version:** 1.0  
**Minimum PHP:** 8.3  
**Framework:** Laravel 13  
**UI:** Livewire 4 + Mary UI 2 + Tailwind CSS 4

---

# 1. Architecture Philosophy

The 8bit Laravel Starter is a reusable foundation for Laravel business applications.

The architecture must remain:

- Laravel-native
- Simple to understand
- Easy for new developers to join
- Easy for Claude Code to work with
- Testable
- Upgradeable
- Modular where useful
- Free from unnecessary abstractions

## Core principle

> Use Laravel's conventions first. Add an abstraction only when it solves a real recurring problem.

The starter must not attempt to replace Laravel's architecture.

---

# 2. High-Level Architecture

The application follows this general flow:

```text
Browser
   │
   ▼
Livewire Component
   │
   ├── Validation
   ├── Authorization
   └── User interaction
   │
   ▼
Action / Application Logic
   │
   ├── Domain rules
   ├── Transactions
   └── Events
   │
   ▼
Models / Services
   │
   ▼
Database / External Services
```

For simple operations, the Livewire component may interact directly with the model when no meaningful business operation exists.

Do not create an Action merely to wrap:

```php
Model::create($data);
```

Actions are for meaningful application operations.

---

# 3. Application Areas: Guest, Member, and Admin

The 8bit Laravel Starter contains three application areas:

```text
Guest / Public Website
Authenticated Member Application
Admin Application
```

They are **not** separate repositories, separate Laravel applications, or separate deployments.

All three areas remain within the same:

```text
Laravel application
Git repository
Database
Deployment
```

The separation happens at the application/UI/route/authorization level, not at the infrastructure level. The starter intentionally stays as close as practical to Laravel's conventional, default project structure — the three areas are a routing/layout/authorization distinction, not a reason to introduce parallel frameworks or directory trees.

## Guest / Public Website

The Guest / Public Website is the unauthenticated, public-facing portion of the application.

It uses:

```text
resources/views/layouts/guest.blade.php
```

`guest.blade.php` is the public/guest presentation layout. It is not restricted to authentication pages — it is the layout for any guest-facing page, whether a login screen or a public marketing page.

Typical future routes may include:

```text
/
/about
/services
/contact
```

These are architectural examples only. They are not implemented by this starter and must not be created merely to satisfy this document.

Public pages may be regular Blade views or Livewire components depending on whether interactivity is required. A dedicated `app/Livewire/Public/` namespace is not introduced merely for organizational symmetry — see Application Area File Organization.

Client-specific public website pages and modules will be added by individual projects built from this starter.

## Authenticated Member Application

The Authenticated Member Application is the non-administrative, authenticated portion of the application — the ordinary functionality available to any signed-in user.

It uses:

```text
resources/views/layouts/app.blade.php
```

Member-facing Livewire components live directly under `app/Livewire/`, close to Laravel's conventional structure, rather than under a dedicated namespace:

```text
app/Livewire/
├── Dashboard.php
└── Profile.php
```

Both require:

```text
auth
verified
```

and are registered in `routes/web.php`:

```text
/dashboard
/profile
```

Profile represents the currently authenticated user's own account — see User Profile vs User Management.

## Admin Application

The Admin Application is a separate, authenticated application area within the same Laravel project.

The canonical URL prefix is:

```text
/admin
```

The Admin Application will eventually contain routes such as:

```text
/admin
/admin/dashboard
/admin/users
/admin/roles
/admin/permissions
/admin/settings
```

Only `/admin/dashboard` exists today. The remaining routes are architectural examples only and must not be created merely to satisfy this document. Phase 3 and later phases will implement them deliberately.

The Admin Application has its own:

```text
Layout
Navigation
Sidebar
Top bar
Page structure
Livewire namespace
Views
Authorization boundary
```

It uses:

```text
resources/views/layouts/admin.blade.php
```

and its Livewire components live under:

```text
app/Livewire/Admin/
```

---

# 4. Application Area File Organization

Guest, Member, and Admin interfaces do not share one giant application layout or view tree.

## Layouts

```text
resources/views/layouts/
├── guest.blade.php
├── app.blade.php
└── admin.blade.php
```

Responsibilities:

```text
guest.blade.php  → Guest / Public Website + guest-facing auth screens
app.blade.php    → Authenticated Member Application
admin.blade.php  → Admin Application
```

The Member and Admin layouts may look visually similar, especially early in the starter's life. They remain separate files because their navigation, available actions, application context, and authorization requirements are expected to diverge as the starter grows.

Do not redesign these layouts merely because this document describes them.

## Livewire Organization

```text
app/Livewire/
├── Auth/
│   ├── Login.php
│   ├── Register.php
│   ├── ForgotPassword.php
│   ├── ResetPassword.php
│   └── VerifyEmail.php
│
├── Dashboard.php
├── Profile.php
│
└── Admin/
    ├── Dashboard.php
    ├── Users/
    ├── Roles/
    ├── Permissions/
    ├── Settings/
    └── ...
```

`Auth/` contains authentication screens used by the application's single, unified authentication system.

`Dashboard.php` and `Profile.php` are Member-facing components and live directly under `app/Livewire/`, following Laravel's conventional structure rather than a dedicated namespace.

`Admin/` contains Livewire components for the Admin Application, organized by feature exactly as described in Livewire Architecture.

Do not create `app/Livewire/Public/` merely for organizational symmetry. Public website pages may be regular Blade views, or Livewire components added directly under `app/Livewire/` (or their own feature namespace), when a public feature genuinely requires one.

Do not create empty `Admin/` feature directories (`Users/`, `Roles/`, `Permissions/`, `Settings/`) merely to satisfy this document. Create them when the corresponding components are actually implemented.

## View Organization

```text
resources/views/
├── layouts/
│   ├── guest.blade.php
│   ├── app.blade.php
│   └── admin.blade.php
│
├── livewire/
│   ├── auth/
│   ├── dashboard.blade.php
│   ├── profile.blade.php
│   └── admin/
│       └── dashboard.blade.php
│
└── components/
    └── 8bit/
```

Admin views live under `resources/views/livewire/admin/`. Member views live directly under `resources/views/livewire/`, mirroring Livewire Organization above. Public website pages, when added, may live under `resources/views/` following normal Laravel conventions (for example `resources/views/about.blade.php`) rather than a dedicated `public/` directory created merely for symmetry.

---

# 5. Admin Is Not a Separate Application

The Admin Application is not a separate single-page application, a separate frontend project, or a separate backend service.

The starter does not use:

```text
React
Vue
Inertia
A separate frontend repository
A separate backend repository
```

The Admin Application uses the same stack as the rest of the starter:

```text
Laravel
Blade
Livewire
Mary UI
Tailwind CSS
daisyUI
```

The Guest/Public Website and the Authenticated Member Application use the same underlying stack while presenting their own layout, navigation, and page structure.

Do not introduce a second frontend framework, a second component library, or a parallel build pipeline to support the Admin Application.

---

# 6. Shared Application Layers

Guest/Public, Member, and Admin share nearly everything below the presentation layer.

Shared:

```text
Laravel application
Eloquent models
Database
Authentication system
Authorization system
Actions / Services where appropriate
Jobs
Events
Notifications
Media Library
Activity logging
Validation rules where appropriate
Shared UI/design tokens
Shared reusable components where appropriate
```

Not shared:

```text
Presentation (layouts, views)
Routing
Navigation
Authorization boundaries specific to each area
```

Do not duplicate business logic merely because these areas have different UIs. A Model, Action, Service, Job, Event, or validation rule written for one area should be reused by the others whenever the underlying operation is the same.

---

# 7. Module Architecture — Public and Admin Interfaces

Future business modules may have both a Public interface and an Admin interface.

Example — a Hotel Booking module:

```text
Public
    /rooms
    /booking

Admin
    /admin/rooms
    /admin/bookings
```

Example — an Events module:

```text
Public
    /events

Admin
    /admin/events
```

The underlying domain/application logic (Models, Actions, Services, Jobs, Events) remains shared and reusable between both interfaces, consistent with Shared Application Layers.

Do not create these example modules. They illustrate the intended pattern only.

For applications complex enough to warrant internal domain folders (see Domain Modules), the Public/Admin split remains a separate, complementary concern from that internal code organization.

---

# 8. Admin Application Responsibilities

The Admin Application is the primary management interface for:

```text
Users
Roles
Permissions
Settings
Media
Activity logs
Future business modules
```

These are architectural responsibilities, not implemented functionality. Do not implement Users, Roles, Permissions, Settings, Media, or Activity Log management interfaces as part of documentation-only work.

---

# 9. User Profile vs User Management

User Profile and User Management are different concerns.

```text
User Profile
    An authenticated user managing their own account.

User Management
    An authorized administrator managing other users' accounts.
```

Profile is part of the Authenticated Member Application. It uses `layouts.app` and is accessible from the Member shell's user menu — it is intentionally not part of the Admin Application.

User Management (listing, creating, editing, deactivating, and assigning roles to other users) is an administrative capability that belongs to the Admin Application.

A user editing their own name and email is never the same operation as an administrator editing another user's account, even when both eventually reuse the same underlying Model and validation rules.

Do not implement User Management as part of documentation-only work.

---

# 10. Directory Structure

The base application uses the following structure:

```text
app/
├── Actions/
├── Console/
├── Data/
├── Enums/
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Jobs/
├── Listeners/
├── Livewire/
├── Models/
├── Notifications/
├── Policies/
├── Providers/
├── Services/
└── Support/
```

Additional Laravel directories remain in their normal locations.

---

# 11. Actions

Location:

```text
app/Actions/
```

Actions represent meaningful application operations.

Examples:

```text
CreateBooking
ConfirmBooking
CancelBooking
GenerateInvoice
ProcessPayment
ImportCustomers
PublishSocialPost
```

An Action should generally represent something a user or system actually does.

## Good

```text
ConfirmBooking
GenerateInvoice
ApprovePurchaseOrder
```

## Bad

```text
CreateBookingModel
SaveBooking
GetBooking
UpdateBookingModel
```

Do not turn every model operation into an Action.

---

# 12. Action Structure

Where an Action is appropriate:

```php
final class ConfirmBooking
{
    public function handle(Booking $booking): void
    {
        // business operation
    }
}
```

Actions should:

- Have one clear responsibility
- Be independently testable
- Handle meaningful business operations
- Use database transactions where appropriate
- Delegate specialized functionality to services when needed

Actions should not become enormous classes containing unrelated operations.

---

# 13. Services

Location:

```text
app/Services/
```

Services are reserved for reusable infrastructure or integrations.

Examples:

```text
PaymentGatewayService
SmsService
WhatsAppService
PdfService
ImageService
SocialMediaService
SeoService
```

Do not create a service automatically for every model.

Avoid:

```text
CustomerService
ProductService
BookingService
UserService
```

unless the class genuinely contains reusable domain logic that doesn't belong in an Action or Model.

---

# 14. Models

Location:

```text
app/Models/
```

Models represent persistent application data.

Models may contain:

- Relationships
- Casts
- Accessors
- Mutators
- Scopes
- Small domain methods
- Attribute definitions
- Media configuration
- Permission-related behavior where appropriate

Avoid putting large workflows inside models.

Bad:

```php
$booking->processEntireBookingWorkflow();
```

when that operation involves multiple systems, notifications, payments, and external APIs.

Prefer an Action.

---

# 15. Policies

Location:

```text
app/Policies/
```

Policies are the authoritative location for model-level authorization.

Examples:

```text
UserPolicy
CustomerPolicy
BookingPolicy
InvoicePolicy
ProductPolicy
```

Use Laravel's authorization system.

Do not perform authorization by manually checking roles throughout Livewire components.

Prefer:

```php
$this->authorize('update', $customer);
```

or:

```php
Gate::authorize('update', $customer);
```

Role/permission checks remain backed by Spatie Permission.

---

# 16. Roles and Permissions

Spatie Laravel Permission is the authorization foundation.

The architecture distinguishes:

```text
Roles
Permissions
Policies
```

### Permission

Answers:

> Is this user allowed to perform this capability?

Example:

```text
customers.create
customers.update
customers.delete
```

### Policy

Answers:

> Is this user allowed to perform this action on this specific model?

Example:

```text
Can Ayon update Customer #123?
```

### Role

Groups permissions.

Example:

```text
Manager
    customers.view
    customers.create
    customers.update
    reports.view
```

Do not use roles directly when a policy or permission is more appropriate.

---

# 17. Permission Naming Convention

Permissions use:

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

For specialized operations:

```text
bookings.confirm
bookings.cancel
bookings.check-in
bookings.check-out
```

Use lowercase kebab-case for multi-word actions.

---

# 18. Livewire Architecture

Location:

```text
app/Livewire/
```

Livewire is the primary application UI framework.

Authentication components live under `Auth/`, Admin Application components live under `Admin/` organized by feature, and Member-facing components (`Dashboard.php`, `Profile.php`) live directly under `app/Livewire/`, as described in Application Area File Organization.

Example:

```text
app/Livewire/
└── Admin/
    └── Customers/
        ├── Index.php
        ├── Create.php
        ├── Edit.php
        └── Show.php
```

For larger features:

```text
app/Livewire/
└── Admin/
    └── Bookings/
        ├── Index.php
        ├── Create.php
        ├── Edit.php
        ├── Show.php
        ├── Calendar.php
        └── Components/
            ├── BookingForm.php
            ├── BookingStatus.php
            └── BookingTimeline.php
```

---

# 19. Livewire Views

Views mirror the Livewire component structure, including the application-area directory.

```text
resources/views/
└── livewire/
    └── admin/
        └── customers/
            ├── index.blade.php
            ├── create.blade.php
            ├── edit.blade.php
            └── show.blade.php
```

For nested components:

```text
resources/views/
└── livewire/
    └── admin/
        └── bookings/
            └── components/
                ├── booking-form.blade.php
                ├── booking-status.blade.php
                └── booking-timeline.blade.php
```

---

# 20. Livewire Responsibilities

A Livewire component may handle:

- User interaction
- Form state
- Validation
- Pagination
- Filtering
- Sorting
- Authorization calls
- Calling Actions
- UI state

A Livewire component should not become the primary location for complex business logic.

Prefer:

```text
Livewire
    ↓
Action
    ↓
Models / Services
```

rather than:

```text
Livewire
    ↓
500 lines of business logic
```

---

# 21. Livewire Forms

For complex forms, use dedicated Form Objects where they improve clarity.

Example:

```text
app/Livewire/Forms/
└── CustomerForm.php
```

Use Form Objects when:

- The form is large
- Create/edit share substantial fields
- Validation is complex
- State needs to be reused

Do not create a Form Object for a tiny two-field form merely for consistency.

---

# 22. Validation

Validation must happen at the application boundary.

For Livewire forms:

```php
$this->validate();
```

For HTTP APIs/controllers:

```text
app/Http/Requests/
```

Use Form Requests where appropriate.

Never trust client-provided values.

Authorization and validation are separate concerns.

---

# 23. UI Architecture

The UI stack is:

```text
Livewire
   ↓
8bit UI conventions
   ↓
Mary UI
   ↓
Tailwind CSS / daisyUI
```

This stack is shared identically across the Guest/Public Website, the Authenticated Member Application, and the Admin Application (see Application Areas: Guest, Member, and Admin). Only layout, navigation, and page structure differ between them.

Mary UI is the primary reusable component library.

The application should avoid unnecessary coupling to third-party component implementation details.

---

# 24. 8bit UI Components

Reusable components should live under:

```text
resources/views/components/
```

Potential structure:

```text
resources/views/components/
└── 8bit/
    ├── button.blade.php
    ├── card.blade.php
    ├── empty-state.blade.php
    ├── page-header.blade.php
    ├── permission-gate.blade.php
    └── status-badge.blade.php
```

Only create wrappers where they provide meaningful value.

Do not wrap every Mary UI component unnecessarily.

---

# 25. UI Design Principles

The starter uses:

- Consistent spacing
- Consistent typography
- Consistent form layouts
- Consistent action placement
- Consistent status indicators
- Responsive layouts
- Dark mode support
- Accessible controls
- Keyboard-friendly interactions

Business applications should feel like members of the same 8bit product family.

---

# 26. Page Layout

Application pages should generally follow:

```text
Page
│
├── Breadcrumb
├── Page Header
│   ├── Title
│   ├── Description
│   └── Primary Actions
│
├── Filters / Toolbar
│
├── Main Content
│
└── Secondary Content
```

Example:

```text
Customers

Manage customer records.

[Import] [Export] [+ Add Customer]

[ Search........ ] [Status ▼] [Filters]

┌──────────────────────────────────────────────┐
│ Customer │ Phone │ Status │ Created │ Actions│
└──────────────────────────────────────────────┘
```

This pattern describes Admin Application and authenticated Member Application pages. Guest/Public Website pages follow their own, simpler page conventions once the Public Website is implemented.

---

# 27. CRUD Architecture

A standard CRUD should normally contain:

```text
Index
Create
Edit
Show
Delete
```

Depending on the feature, Create/Edit may share a form.

Example:

```text
Customers/
├── Index.php
├── Create.php
├── Edit.php
└── Show.php
```

Views:

```text
customers/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php
```

---

# 28. CRUD Index Requirements

Where appropriate, index pages should support:

- Search
- Pagination
- Sorting
- Filters
- Empty state
- Loading state
- Authorization
- Bulk actions
- Row actions

Do not automatically add every feature to every table.

A simple table should remain simple.

---

# 29. Tables

Tables should have consistent:

- Header styling
- Row spacing
- Status badges
- Action menus
- Empty states
- Loading states
- Pagination

Tables must work on smaller screens.

For very wide datasets, provide an appropriate responsive strategy rather than allowing the entire page to overflow.

---

# 30. Notifications

The starter does not use Livewire Alert.

Notifications use:

```text
Laravel
+
Livewire
+
8bit notification convention
+
Mary UI
```

Supported types:

```text
success
error
warning
info
```

Example application-level API:

```php
$this->notifySuccess('Customer created successfully.');
```

The exact implementation will be defined in `CONVENTIONS.md`.

Business code should not depend directly on a specific JavaScript notification library.

---

# 31. Confirmation Dialogs

Destructive actions should use confirmation dialogs.

Example:

```text
Delete Customer?

This action cannot be undone.

[Cancel] [Delete]
```

The confirmation mechanism should be reusable and consistent.

Never perform destructive operations from a UI without appropriate confirmation where accidental activation is plausible.

---

# 32. Settings Architecture

Settings are global application configuration stored in the database.

Suggested structure:

```text
settings
├── General
├── Company
├── Contact
├── Appearance
├── Email
├── Notifications
└── SEO
```

Settings should be accessed through a centralized settings service/helper.

Avoid:

```php
Setting::where('key', 'company.name')->first();
```

throughout application code.

Prefer:

```php
setting('company.name');
```

or the finalized 8bit Settings API.

---

# 33. Media Architecture

Spatie Media Library is the media foundation.

Models requiring media implement the appropriate media interfaces/traits.

Base collections:

```text
avatar
logo
documents
attachments
```

Projects may define additional collections.

Media should be accessed through Media Library rather than direct filesystem manipulation whenever the file represents application-managed media.

---

# 34. Activity Architecture

Where Activitylog is enabled, activity tracking should be applied selectively.

Log meaningful events such as:

```text
Created
Updated
Deleted
Approved
Rejected
Published
Status changed
Permission changed
```

Do not log every insignificant internal operation.

Sensitive data should not be written into activity logs unnecessarily.

---

# 35. Events and Listeners

Use Laravel Events when:

- Multiple parts of the application need to react to an event
- The operation is naturally event-driven
- Side effects should be decoupled

Examples:

```text
BookingConfirmed
InvoicePaid
UserRegistered
OrderCompleted
```

Listeners may handle:

```text
SendNotification
GenerateDocument
UpdateStatistics
SyncExternalSystem
```

Do not create events merely to add ceremony to simple CRUD.

---

# 36. Jobs

Location:

```text
app/Jobs/
```

Jobs are used for:

- Long-running operations
- External API calls
- Emails where appropriate
- Report generation
- Imports
- Exports
- Image processing
- Social publishing
- Scheduled processing

Example:

```text
GenerateMonthlyReport
ProcessCustomerImport
PublishSocialPost
GenerateInvoicePdf
```

Keep HTTP/Livewire requests fast whenever practical.

---

# 37. Database Transactions

Use transactions for operations that must succeed or fail together.

Example:

```text
Create Booking
    ↓
Create Booking Items
    ↓
Create Payment
    ↓
Update Inventory
```

If these operations must remain consistent, wrap them in a transaction.

Do not use transactions blindly around every database operation.

---

# 38. Enums

Location:

```text
app/Enums/
```

Use PHP enums for finite states.

Examples:

```text
BookingStatus
PaymentStatus
UserStatus
OrderStatus
```

Prefer:

```php
BookingStatus::Confirmed
```

over scattered magic strings.

---

# 39. Data Objects

Location:

```text
app/Data/
```

Data objects are optional.

Use them where they improve:

- Complex data transfer
- External API payloads
- Complex workflows
- Reusable structured input/output

Do not introduce DTOs for every model.

---

# 40. API Architecture

When Sanctum/API support is enabled:

```text
routes/
└── api.php
```

Use:

```text
/api/v1/
```

API resources should live according to normal Laravel conventions.

API responses should be predictable and versioned.

Do not expose internal database structure unnecessarily.

---

# 41. External Integrations

External services should be isolated.

Examples:

```text
app/Services/
├── Payment/
├── Sms/
├── WhatsApp/
├── Social/
└── Storage/
```

Avoid embedding provider-specific API calls directly inside Livewire components.

Bad:

```text
Livewire
    ↓
HTTP request to Facebook API
```

Prefer:

```text
Livewire
    ↓
Action
    ↓
Service
    ↓
Provider API
```

---

# 42. Configuration

Application configuration belongs in:

```text
config/
```

Environment-specific values belong in:

```text
.env
```

Never hard-code:

- API keys
- Passwords
- Tokens
- Production URLs
- Provider credentials

---

# 43. Routes

Use normal Laravel route organization.

```text
routes/
├── web.php
├── auth.php
├── admin.php
├── console.php
└── api.php
```

`web.php`: Guest/Public website routes, plus Authenticated Member Application routes (`/dashboard`, `/profile`).

`auth.php`: Authentication routes (login, registration, password reset, email verification, logout). Already established in this starter.

`admin.php`: Admin Application routes, registered under the `/admin` prefix. See Admin Route Structure. Already established in this starter, currently registering only the Admin Dashboard.

Admin routes should use a consistent prefix:

```text
/admin
```

and appropriate authentication/authorization middleware.

---

# 44. Admin Route Structure

Admin routes belong in a dedicated route file:

```text
routes/admin.php
```

registered from `routes/web.php` the same way authentication routes are already registered:

```php
require __DIR__.'/admin.php';
```

Recommended route group:

```php
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // routes
    });
```

`routes/admin.php` currently registers the Admin Dashboard only. Additional Admin routes (Users, Roles, Permissions, Settings) are added as those features are actually implemented, not merely to satisfy this document.

Individual resources should additionally enforce authorization.

Authentication alone is not authorization.

## Authentication vs Authorization

These are separate questions:

```text
Authentication
    Who is the user?

Authorization
    What is the user allowed to do?
```

The Admin Application is not:

```text
authenticated → access everything
```

Every Admin route and action must additionally enforce authorization, using the authorization architecture already established in this document (see Roles and Permissions, Policies).

The eventual Admin authorization system combines:

```text
Laravel authorization conventions (Policies, Gates)
+
Spatie Laravel Permission
```

Phase 3 establishes the exact permission set, roles, and policy implementation. Do not implement authorization middleware, permissions, or roles as part of documentation-only work.

---

# 45. Middleware

Use middleware for cross-cutting HTTP concerns.

Examples:

```text
Authenticate
EnsureUserIsActive
SetLocale
SetTimezone
```

Do not put business operations into middleware.

---

# 46. Exceptions

Use Laravel's exception system.

Create custom exceptions only when they provide meaningful semantic value.

Examples:

```text
InsufficientStock
PaymentFailed
BookingNotAvailable
```

Avoid creating custom exception classes for every possible error.

---

# 47. Caching

Use Laravel's Cache abstraction.

Never make application code dependent directly on Redis unless Redis is a documented project requirement.

This allows:

```text
Database cache
Redis cache
Other supported drivers
```

without rewriting application code.

---

# 48. Queues

The base starter uses:

```text
database
```

as the default queue driver.

Projects requiring high-volume processing may switch to Redis/Horizon.

Queue-specific infrastructure must remain optional.

---

# 49. Scheduling

Use Laravel's scheduler.

Scheduled operations should be:

- Idempotent where possible
- Safe to retry
- Logged appropriately
- Efficient

Long-running scheduled work should dispatch Jobs instead of doing everything inside the scheduler itself.

---

# 50. Search

Basic database search is the default.

Laravel Scout is optional.

Do not introduce an external search engine unless the application actually requires it.

---

# 51. Reporting

Reports should be implemented according to project requirements.

Do not build a generic report engine into the base starter.

Reusable infrastructure may include:

```text
PDF generation
CSV export
Excel export
Queued report generation
```

as optional modules.

---

# 52. Multi-Tenancy

Multi-tenancy is explicitly excluded from v1 core architecture.

Future SaaS projects may introduce tenant isolation as a separate architectural layer.

Do not add:

```text
tenant_id
```

to every base table simply because future projects may need it.

---

# 53. Domain Modules

Small and medium applications should use normal Laravel structure.

For large applications, domain modules may be introduced.

Example:

```text
app/
└── Domains/
    ├── Booking/
    ├── Inventory/
    ├── Customers/
    └── Accounting/
```

Do not introduce domain modules prematurely.

The trigger should be actual application complexity, not architectural fashion.

This internal complexity axis is independent of, and complementary to, the Public/Admin interface split described in Module Architecture — Public and Admin Interfaces.

---

# 54. Dependency Direction

Preferred dependency direction:

```text
UI
 ↓
Application Operations
 ↓
Domain / Models
 ↓
Infrastructure
```

Examples:

```text
Livewire
    ↓
CreateCustomer
    ↓
Customer
```

or:

```text
Livewire
    ↓
PublishSocialPost
    ↓
SocialMediaService
    ↓
Provider API
```

Avoid circular dependencies.

---

# 55. Business Logic Rule

Business logic must have a clear home.

Use:

| Logic | Preferred location |
|---|---|
| Validation | Livewire Form / Form Request |
| Authorization | Policy / Permission |
| Simple model behavior | Model |
| Business operation | Action |
| External integration | Service |
| Async work | Job |
| Cross-component reaction | Event/Listener |
| UI state | Livewire |
| Presentation | Blade |
| Configuration | Config / Settings |

---

# 56. Testing Architecture

Tests mirror application behavior.

```text
tests/
├── Feature/
│   ├── Auth/
│   ├── Users/
│   ├── Roles/
│   ├── Settings/
│   ├── Media/
│   └── ...
│
└── Unit/
```

Feature tests are preferred for:

- User workflows
- Authorization
- Livewire components
- Database behavior
- APIs
- Actions

Unit tests are appropriate for isolated logic.

---

# 57. Claude Code Architecture Rules

Claude Code must follow this architecture.

Before creating a new class, determine:

1. Is this Laravel functionality already available?
2. Is this a UI concern?
3. Is this validation?
4. Is this authorization?
5. Is this a business operation?
6. Is this an external integration?
7. Is this asynchronous work?
8. Is an abstraction actually necessary?

Do not create abstractions merely because they appear architecturally elegant.

---

# 58. Claude Code — Package Rules

Claude Code must not install a new Composer or NPM package without:

1. Checking whether existing Laravel functionality solves the problem.
2. Checking existing 8bit infrastructure.
3. Checking PHP 8.3 compatibility.
4. Checking Laravel 13 compatibility.
5. Checking maintenance status.
6. Checking license.
7. Documenting why the dependency is necessary.

---

# 59. Claude Code — Modification Rules

Before changing core infrastructure:

```text
Inspect
 ↓
Understand existing convention
 ↓
Implement smallest appropriate change
 ↓
Test
 ↓
Review
```

Do not rewrite working architecture unnecessarily.

Do not migrate from one package/library to another simply because a newer alternative exists unless explicitly requested.

---

# 60. Claude Code — New Feature Workflow

For a new feature:

```text
Requirement
   ↓
Identify domain
   ↓
Identify models
   ↓
Identify permissions
   ↓
Identify policy
   ↓
Identify Action(s)
   ↓
Build Livewire UI
   ↓
Build Blade views
   ↓
Add tests
   ↓
Run quality checks
```

For simple CRUD:

```text
Model
 ↓
Policy
 ↓
Permissions
 ↓
Livewire CRUD
 ↓
Tests
```

Do not add unnecessary Actions/Services.

---

# 61. Security Rule

Claude Code must never bypass authorization, validation, or security controls merely to make a feature work.

Never:

```php
$user->is_admin
```

when the application authorization system already provides the appropriate permission/policy mechanism.

Never expose sensitive records merely because the user can reach a URL.

Always consider:

- Authorization
- Validation
- Mass assignment
- CSRF
- Rate limiting
- File validation
- Data exposure
- SQL injection
- XSS
- Secure credentials

---

# 62. Performance Rule

Optimize based on actual requirements.

Avoid premature optimization.

However, Claude Code should watch for common Laravel problems:

- N+1 queries
- Unbounded queries
- Missing pagination
- Loading unnecessary relationships
- Large synchronous jobs
- Repeated expensive queries
- Missing indexes

Use eager loading where appropriate.

---

# 63. Database Design

Database schema should prioritize:

- Clear naming
- Proper foreign keys
- Appropriate indexes
- Explicit relationships
- Appropriate nullable fields
- Appropriate constraints

Avoid premature denormalization.

Business-specific schema belongs to the application, not the starter.

---

# 64. Naming Conventions

Use standard Laravel naming.

### Classes

```text
PascalCase
```

### Methods

```text
camelCase
```

### Database tables

```text
snake_case plural
```

### Database columns

```text
snake_case
```

### Livewire components

```text
PascalCase
```

### Blade views

```text
kebab-case
```

---

# 65. Git-Friendly Architecture

Generated files should be deterministic and easy to review.

Avoid generating huge files with unrelated responsibilities.

Each class should have a clear reason to exist.

Changes should be small enough to understand in a pull request.

---

# 66. Upgradeability

The starter must remain close to upstream Laravel conventions.

Avoid modifying framework internals.

Avoid unnecessary monkey patches.

Avoid dependencies with overlapping responsibilities.

When Laravel provides a feature that replaces a custom implementation, prefer the Laravel implementation during a planned upgrade.

---

# 67. Final Architecture Principle

The 8bit Laravel Starter should feel like:

```text
Laravel
+
Opinionated 8bit conventions
+
A small number of high-value packages
+
Reusable UI patterns
+
Excellent developer tooling
```

It should NOT feel like:

```text
Laravel
+
A second framework
+
A custom ORM
+
A custom authorization system
+
A custom UI framework
+
Dozens of abstractions
```

The goal is not to hide Laravel.

The goal is to make experienced Laravel developers faster and make new 8bit projects start from a mature foundation.
