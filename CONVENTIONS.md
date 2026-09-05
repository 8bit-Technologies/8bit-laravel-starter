# 8bit Laravel Starter v1
## Development Conventions

**Organization:** 8bit Technologies  
**Starter:** 8bit Laravel Starter  
**Version:** 1.0  
**Minimum PHP:** 8.3  
**Laravel:** 13  
**Livewire:** 4  
**UI:** Mary UI 2 + Tailwind CSS 4 + daisyUI 5

---

# 1. Purpose

This document defines the coding and development conventions for applications built using the 8bit Laravel Starter.

These conventions exist to ensure that:

- Projects feel familiar across 8bit Technologies.
- Claude Code generates predictable code.
- Developers can move between projects easily.
- Features have consistent structure.
- Business logic remains maintainable.
- UI remains visually consistent.
- Applications remain close to Laravel conventions.

---

# 2. Golden Rule

> Follow Laravel conventions unless there is a documented 8bit convention that provides a clear improvement.

Do not introduce architecture for architecture's sake.

Prefer the simplest solution that correctly solves the problem.

---

# 3. Feature-First Thinking

When implementing a feature, first identify the feature/domain.

Example:

```text
Customers
Bookings
Invoices
Inventory
Users
Reports
Settings
```

Then organize related code around that feature while still respecting Laravel's standard application directories.

Example:

```text
app/
├── Actions/
│   └── Customers/
│       ├── CreateCustomer.php
│       └── DeleteCustomer.php
│
├── Livewire/
│   └── Customers/
│       ├── Index.php
│       ├── Create.php
│       ├── Edit.php
│       └── Show.php
│
├── Models/
│   └── Customer.php
│
└── Policies/
    └── CustomerPolicy.php
```

---

# 4. Naming Conventions

## Classes

Use PascalCase.

```php
Customer
CustomerPolicy
CreateCustomer
CustomerForm
CustomerNotification
```

---

## Methods

Use camelCase.

```php
createCustomer()
calculateTotal()
sendNotification()
```

---

## Variables

Use descriptive camelCase names.

```php
$customer
$bookingItems
$availableRooms
```

Avoid:

```php
$c
$data1
$x
$temp
```

unless the context makes the variable genuinely obvious.

---

# 5. Database Naming

Tables:

```text
customers
bookings
booking_items
purchase_orders
```

Columns:

```text
first_name
last_name
created_at
updated_at
booking_status
```

Foreign keys:

```text
customer_id
booking_id
user_id
```

Boolean fields should generally use descriptive names:

```text
is_active
is_verified
is_published
```

---

# 6. Models

Models represent persistent data.

Example:

```php
class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
}
```

Use relationships explicitly.

```php
public function bookings(): HasMany
{
    return $this->hasMany(Booking::class);
}
```

---

# 7. Model Responsibilities

Models may contain:

- Relationships
- Casts
- Scopes
- Accessors
- Mutators
- Small domain methods
- Media configuration

Models should not contain large multi-step business workflows.

---

# 8. Mass Assignment

Prefer explicit `$fillable` declarations.

Avoid:

```php
protected $guarded = [];
```

unless there is a specific and documented reason.

Never blindly pass arbitrary request data into:

```php
Model::create($request->all());
```

---

# 9. Enums

Use PHP enums for finite states.

Example:

```php
enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
}
```

Use enums instead of scattered magic strings.

---

# 10. Actions

Actions represent meaningful operations.

Good examples:

```text
CreateCustomer
UpdateCustomer
DeleteCustomer
ConfirmBooking
CancelBooking
GenerateInvoice
ProcessPayment
```

Avoid meaningless wrapper Actions.

Bad:

```text
GetCustomer
SaveCustomer
UpdateCustomerModel
```

unless the operation has actual business significance.

---

# 11. Action Naming

Use a verb + noun.

```text
CreateCustomer
UpdateCustomer
DeleteCustomer
ApproveInvoice
GenerateReport
PublishPost
ProcessPayment
```

For complex operations:

```text
GenerateMonthlySalesReport
ImportCustomers
SyncCustomerWithCrm
```

---

# 12. Action Interface

Actions should generally expose one main entry point:

```php
final class CreateCustomer
{
    public function handle(array $data): Customer
    {
        return Customer::create($data);
    }
}
```

For dependency injection:

```php
public function handle(
    Customer $customer,
    array $data
): Customer
{
    // ...
}
```

Use constructor dependencies for reusable services.

---

# 13. Transactions

Use transactions when multiple changes must succeed or fail together.

Example:

```php
DB::transaction(function () use ($data) {
    // create booking
    // create items
    // update inventory
});
```

Do not wrap every database operation in a transaction unnecessarily.

---

# 14. Livewire Naming

Livewire components use PascalCase PHP classes:

```text
Customers/Index
Customers/Create
Customers/Edit
Customers/Show
```

Corresponding views:

```text
resources/views/livewire/customers/index.blade.php
resources/views/livewire/customers/create.blade.php
resources/views/livewire/customers/edit.blade.php
resources/views/livewire/customers/show.blade.php
```

---

# 15. CRUD Convention

Standard CRUD:

```text
Index
Create
Edit
Show
```

Delete is generally an Action triggered from Index/Show/Edit rather than requiring a dedicated page.

Example:

```text
Customers/
├── Index.php
├── Create.php
├── Edit.php
└── Show.php
```

---

# 16. Index Page

Index pages should normally contain:

```text
Page Header
Toolbar
Filters
Table/List
Pagination
Empty State
```

Example:

```text
Customers

Manage your customers.

[+ Add Customer]

[ Search customers... ] [Status ▼]

┌───────────────────────────────────────────┐
│ Name │ Email │ Phone │ Status │ Actions   │
├───────────────────────────────────────────┤
│ ...                                       │
└───────────────────────────────────────────┘

              ← 1 2 3 4 →
```

---

# 17. Search

Search fields should use a predictable property:

```php
public string $search = '';
```

For multiple search fields:

```php
public string $name = '';
public string $email = '';
```

Avoid unnecessarily complex search abstractions for simple tables.

---

# 18. Pagination

Use Livewire pagination for large lists.

Do not load thousands of records into memory simply to display a table.

Prefer:

```php
Customer::query()
    ->paginate(25);
```

Default pagination size should generally be:

```text
25
```

unless the application's UX requires otherwise.

---

# 19. Sorting

Use explicit sortable columns.

Do not dynamically accept arbitrary database column names from the browser.

Maintain an allowlist of sortable fields.

Example:

```php
protected array $sortable = [
    'name',
    'created_at',
];
```

---

# 20. Filtering

Filters should be:

- Explicit
- Validated
- Resettable
- Bookmarkable where useful

Example:

```text
Status
[ All ▼ ]

Created
[ From ] [ To ]
```

Provide a clear:

```text
Clear Filters
```

action when multiple filters exist.

---

# 21. Forms

Simple forms may live directly in the Livewire component.

Complex forms should use Livewire Form Objects.

Example:

```text
app/Livewire/Forms/CustomerForm.php
```

A Form Object is appropriate when:

- The form has many fields.
- Create and Edit share fields.
- Validation is substantial.
- State management is becoming difficult.

---

# 22. Form Naming

Use meaningful property names.

Good:

```php
public string $name = '';
public string $email = '';
public ?string $phone = null;
```

Avoid:

```php
public $field1;
public $field2;
```

---

# 23. Validation

Validation should be defined as close as practical to the form boundary.

Example:

```php
protected function rules(): array
{
    return [
        'form.name' => ['required', 'string', 'max:255'],
        'form.email' => ['required', 'email'],
    ];
}
```

Validation messages should be user-friendly.

---

# 24. Authorization

Authorization must happen before sensitive operations.

Preferred:

```php
$this->authorize('update', $customer);
```

or:

```php
Gate::authorize('update', $customer);
```

Do not rely only on hiding a button.

The backend operation must also enforce authorization.

---

# 25. Permissions

Permission names follow:

```text
{verb} {resource}
```

The permission name is the exact Laravel authorization ability — there is no separate internal identifier or dot-notation form to keep in sync. The same string is used everywhere authorization is checked:

```php
@can('access dashboard')

auth()->user()->can('access dashboard')

Route::middleware('can:access dashboard')
```

Examples:

```text
access dashboard

view users
create users
update users
delete users
```

Special actions:

```text
confirm bookings
cancel bookings
check in bookings
check out bookings
```

Do not use dot notation (`resource.action`) for permission names, and do not introduce a second internal permission identifier separate from the display name. See `PHASE-3-ROLES-PERMISSIONS.md` for the full authorization architecture this convention supports.

---

# 26. Policy Convention

Every significant model exposed through an authenticated application should have a Policy when authorization is required.

Example:

```text
app/Policies/CustomerPolicy.php
```

Policy methods should follow Laravel conventions:

```text
viewAny
view
create
update
delete
restore
forceDelete
```

Custom business actions may be added:

```text
confirm
cancel
approve
publish
```

---

# 27. Roles

Roles should group permissions.

Example:

```text
Administrator
Manager
Staff
Accountant
Viewer
```

Do not scatter role names throughout application logic.

Prefer permission checks or policies.

---

# 28. Role Checks

Avoid:

```php
if ($user->role === 'admin') {
    ...
}
```

Prefer:

```php
$user->can('delete customers')
```

or appropriate policy authorization.

Direct role checks should be reserved for genuine role-specific behavior where permissions are insufficient.

---

# 29. Navigation Authorization

Navigation items requiring permissions should be hidden when the user lacks permission.

However:

> Hiding a navigation item is UX, not security.

The underlying route/component/action must still authorize the operation.

---

# 30. Mary UI

Mary UI is the default UI component library.

Prefer Mary UI components over creating custom implementations of common controls.

Use custom 8bit components when:

- A pattern repeats across projects.
- A business-specific component is required.
- Mary UI doesn't provide the desired UX.
- A consistent 8bit design pattern needs to be enforced.

---

# 31. 8bit Components

Reusable application-level components live under:

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

Do not create an 8bit wrapper merely to rename a Mary UI component.

---

# 32. Page Header

Every major application page should use a consistent page header.

Example:

```text
Customers

Manage customer records and contact information.

[Import] [Export] [+ Add Customer]
```

The primary action should be visually obvious.

---

# 33. Buttons

Use consistent button semantics.

Primary:

```text
Save
Create Customer
Confirm Booking
```

Secondary:

```text
Cancel
Back
View
```

Destructive:

```text
Delete
Remove
Cancel Booking
```

Avoid vague labels such as:

```text
Submit
Click Here
Do It
```

when a more meaningful action is possible.

---

# 34. Status Badges

Statuses should use a consistent visual convention.

Examples:

```text
Pending
Confirmed
Cancelled
Completed
Active
Inactive
```

Use an 8bit `status-badge` component where the same status appears repeatedly.

Status color mappings should be centralized rather than duplicated throughout Blade files.

---

# 35. Empty States

Every data-driven page should have a useful empty state.

Bad:

```text
No data.
```

Better:

```text
No customers yet.

Create your first customer to get started.

[+ Add Customer]
```

Empty states may include a relevant action.

---

# 36. Loading States

Interactive Livewire components should provide appropriate loading feedback.

For example:

```blade
wire:loading
```

Use loading states for:

- Save
- Delete
- Search
- Filtering
- Long-running interactions

Do not make the entire screen unusable for a small local operation unless necessary.

---

# 37. Notifications

The 8bit Starter does not use Livewire Alert.

Use the 8bit notification convention.

Preferred application-level API:

```php
$this->notifySuccess('Customer created successfully.');
$this->notifyError('Unable to create customer.');
$this->notifyWarning('This customer has outstanding invoices.');
$this->notifyInfo('Report generation has started.');
```

The underlying implementation may use session flash messages and/or Livewire browser events.

Business logic must not depend directly on Mary UI notification internals.

---

# 38. Notification Messages

Success messages should be concise.

Good:

```text
Customer created successfully.
```

Bad:

```text
The customer has been successfully and wonderfully created into the system.
```

Error messages should explain what the user can do next when possible.

---

# 39. Confirmation Dialogs

Destructive operations should use confirmation dialogs when accidental activation is plausible.

Example:

```text
Delete Customer?

This action cannot be undone.

[Cancel] [Delete]
```

The destructive button should be visually distinct.

---

# 40. Delete Operations

Deletes must:

1. Authorize.
2. Confirm where appropriate.
3. Perform the operation.
4. Notify the user.
5. Refresh the UI where necessary.

Example flow:

```text
User clicks Delete
        ↓
Confirmation
        ↓
Policy authorization
        ↓
Delete Action
        ↓
Success notification
        ↓
Refresh list
```

---

# 41. Flash Messages

Use session flash messages for server-side notifications where appropriate.

Do not create multiple competing flash-message systems.

---

# 42. Media

Use Spatie Media Library for managed files.

Do not scatter:

```php
Storage::put(...)
```

throughout business code when the file is an application-managed media asset.

---

# 43. Media Collection Naming

Use descriptive collections.

Common:

```text
avatar
logo
documents
attachments
```

Domain-specific:

```text
gallery
room-images
product-images
event-banner
```

Use plural names where the collection represents multiple assets.

---

# 44. File Upload Validation

All uploaded files must be validated.

Consider:

- MIME type
- Extension
- Maximum size
- Image dimensions
- Security
- Storage location

Never trust the extension supplied by the browser.

---

# 45. Settings

Use a centralized Settings API.

Preferred usage:

```php
setting('company.name');
```

or the finalized 8bit Settings service.

Do not repeatedly query settings directly from application code.

Bad:

```php
Setting::where('key', 'company.name')->first();
```

---

# 46. Settings Naming

Use dot notation:

```text
company.name
company.email
company.phone

appearance.logo
appearance.favicon

seo.default_title
seo.default_description
```

Keep names predictable.

---

# 47. Jobs

Use Jobs for operations that may take significant time.

Examples:

```text
GenerateInvoicePdf
ImportCustomers
ExportCustomers
PublishSocialPost
ProcessMedia
GenerateReport
```

Jobs should be retry-safe where practical.

---

# 48. Job Naming

Use a verb describing the operation:

```text
GenerateReport
ProcessImport
SendInvoice
PublishPost
SyncCustomer
```

Avoid:

```text
ReportJob
CustomerJob
GenericJob
```

---

# 49. Events

Events represent something that happened.

Examples:

```text
BookingConfirmed
InvoicePaid
CustomerRegistered
OrderCompleted
```

Do not name events as commands.

Bad:

```text
ConfirmBooking
```

if the event represents the fact that confirmation already happened.

Better:

```text
BookingConfirmed
```

---

# 50. Listeners

Listeners react to events.

Example:

```text
BookingConfirmed
    ↓
SendBookingConfirmation
```

Use listeners when multiple independent consumers may react to an event.

---

# 51. Services

Services are primarily for reusable infrastructure or external integrations.

Good:

```text
PaymentGatewayService
WhatsAppService
SmsService
PdfService
SocialMediaService
```

Do not create generic services simply because every feature "should have a service."

---

# 52. External APIs

Never call an external API directly from a Livewire component.

Bad:

```text
Livewire
  ↓
HTTP request
  ↓
Facebook
```

Prefer:

```text
Livewire
  ↓
Action
  ↓
Service
  ↓
External API
```

Long-running external operations should generally be queued.

---

# 53. API Conventions

APIs should use:

```text
/api/v1
```

Example:

```text
/api/v1/customers
/api/v1/bookings
/api/v1/products
```

Use Laravel API Resources where they improve response consistency.

Do not expose database records blindly.

---

# 54. API Authorization

API authentication and authorization are separate concerns.

Authentication answers:

> Who are you?

Authorization answers:

> Are you allowed to perform this operation?

Both must be implemented.

---

# 55. Database Queries

Prefer Eloquent and Query Builder.

Avoid raw SQL unless it provides a clear benefit.

Always consider:

- Indexes
- Eager loading
- Pagination
- Query count
- Result size

---

# 56. N+1 Prevention

When displaying relationships:

```php
Customer::with('bookings')->get();
```

rather than triggering individual relationship queries for every row.

Claude Code should actively inspect for potential N+1 queries.

---

# 57. Query Limits

Never assume a database query will remain small.

Bad:

```php
Customer::all();
```

for a table that may contain thousands of records.

Prefer:

```php
Customer::paginate(25);
```

or chunking/streaming for processing.

---

# 58. Database Indexes

Add indexes for columns frequently used for:

- Filtering
- Searching
- Sorting
- Foreign-key relationships
- Unique lookups

Do not add indexes blindly.

---

# 59. Route Model Binding

Prefer Laravel route model binding.

Example:

```php
Route::get('/customers/{customer}', ...);
```

rather than manually resolving IDs everywhere.

---

# 60. Controllers

Controllers should remain thin.

A controller should generally:

```text
Receive request
 ↓
Authorize
 ↓
Validate
 ↓
Call Action
 ↓
Return response
```

Do not put large business workflows into controllers.

---

# 61. Livewire vs Controllers

Use Livewire for interactive web application interfaces.

Use controllers for:

- Traditional HTTP endpoints
- API endpoints
- File downloads
- Webhooks
- Special HTTP responses

Do not force every HTTP operation into Livewire.

---

# 62. Blade

Blade templates should primarily handle presentation.

Avoid large PHP blocks inside Blade.

Bad:

```blade
@php
    // 100 lines of business logic
@endphp
```

Move logic into the appropriate PHP class.

---

# 63. Tailwind

Prefer utility classes.

Keep repeated styling patterns in reusable components when they become substantial.

Avoid giant unreadable class strings when a reusable component would be clearer.

---

# 64. Responsive Design

All application interfaces must consider:

```text
Mobile
Tablet
Desktop
Large desktop
```

Tables, forms, dialogs and navigation must remain usable on smaller screens.

---

# 65. Accessibility

UI components should provide:

- Proper labels
- Keyboard navigation
- Visible focus states
- Meaningful button text
- Appropriate ARIA attributes where necessary
- Sufficient contrast
- Error messages associated with fields

Accessibility should not be treated as a final-stage task.

---

# 66. Dark Mode

The starter should support dark mode through the selected UI stack.

Components should not hard-code colors in a way that breaks dark mode.

---

# 67. Logging

Use Laravel's logging system.

Log:

- Important failures
- External integration failures
- Critical business events where useful

Do not log:

- Passwords
- API secrets
- Authentication tokens
- Sensitive personal data unnecessarily

---

# 68. Exceptions

Use exceptions for exceptional conditions.

Create custom exceptions when they communicate meaningful business state.

Examples:

```text
InsufficientStock
BookingUnavailable
PaymentFailed
```

Do not create hundreds of trivial exception classes.

---

# 69. Security

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

Never disable a security mechanism simply to make development easier.

---

# 70. Tests

Every meaningful feature should include tests.

Minimum expectations for a typical CRUD:

```text
Create works
Update works
Delete works
Validation works
Authorization works
```

For important business operations, test failure conditions as well.

---

# 71. Livewire Tests

Livewire interactions should be tested at the feature level.

Examples:

```text
Component renders
Validation works
User can create record
Unauthorized user cannot create record
Filters work
Pagination works
Delete confirmation works
```

---

# 72. Test Naming

Tests should describe behavior.

Good:

```text
it('allows an authorized user to create a customer')
```

Bad:

```text
it('customer test')
```

---

# 73. Factories

Every major model should generally have a Factory.

Example:

```text
CustomerFactory
BookingFactory
InvoiceFactory
```

Factories should provide sensible defaults.

Use states for common scenarios:

```text
Customer::factory()->inactive()
Booking::factory()->confirmed()
```

---

# 74. Seeders

Seeders should provide realistic development/demo data where useful.

Roles and permissions should have deterministic seeders.

Example:

```text
RolePermissionSeeder
DemoDataSeeder
```

Do not mix production configuration with fake demo data.

---

# 75. Database Seeders

Permission/role seeders should be safe to run repeatedly.

Prefer:

```text
firstOrCreate
```

or equivalent idempotent behavior.

Avoid creating duplicates each time.

---

# 76. Environment Variables

Environment variables belong in `.env`.

Never commit secrets.

Provide `.env.example` with required keys documented.

---

# 77. Configuration

Use configuration files for application configuration.

Do not repeatedly call:

```php
env(...)
```

inside application code.

Use:

```php
config(...)
```

instead.

---

# 78. Artisan Commands

Custom commands should have descriptive names.

Examples:

```text
customers:import
reports:generate
media:cleanup
social:publish
```

Commands should delegate meaningful work to Actions/Services/Jobs rather than contain enormous workflows.

---

# 79. Scheduled Tasks

Scheduled tasks should generally dispatch Jobs for expensive work.

Example:

```text
Scheduler
    ↓
GenerateDailyReport Job
```

rather than:

```text
Scheduler
    ↓
30-minute database/API operation
```

---

# 80. Code Formatting

All PHP code must be formatted using Laravel Pint.

Before committing:

```bash
vendor/bin/pint
```

---

# 81. Static Analysis

Run Larastan/PHPStan regularly.

The goal is to catch:

- Invalid types
- Undefined methods
- Incorrect relationships
- Impossible conditions
- Return-type problems

---

# 82. Testing Commands

Typical verification:

```bash
php artisan test
```

or:

```bash
./vendor/bin/pest
```

Then:

```bash
vendor/bin/pint --test
```

and the configured PHPStan/Larastan command.

---

# 83. Claude Code Workflow

Claude Code must follow this sequence when implementing a feature:

```text
1. Understand requirement
        ↓
2. Inspect existing code
        ↓
3. Identify existing conventions
        ↓
4. Identify affected models
        ↓
5. Identify permissions/policies
        ↓
6. Identify Action/Service requirements
        ↓
7. Implement UI
        ↓
8. Implement business logic
        ↓
9. Add tests
        ↓
10. Run quality checks
        ↓
11. Review changes
```

---

# 84. Claude Code — Do Not Guess

Claude Code must inspect the existing project before assuming:

- Package versions
- File locations
- Component APIs
- Existing helpers
- Existing permissions
- Existing UI patterns
- Existing database schema

If the codebase already has a convention, follow it.

---

# 85. Claude Code — No Unnecessary Refactoring

When asked to implement a feature:

> Do not rewrite unrelated working code.

Avoid:

- Renaming unrelated classes
- Reformatting unrelated files
- Migrating packages
- Reorganizing the entire application
- Rebuilding existing components

unless explicitly requested or necessary.

---

# 86. Claude Code — Dependencies

Before adding a package:

```text
Check Laravel
 ↓
Check existing package
 ↓
Check 8bit infrastructure
 ↓
Check compatibility
 ↓
Check maintenance
 ↓
Add only if justified
```

Never add dependencies casually.

---

# 87. Claude Code — UI

When building UI:

1. Check existing 8bit components.
2. Check Mary UI.
3. Reuse existing patterns.
4. Create a reusable component if the pattern is genuinely repeated.
5. Maintain responsive and accessible behavior.

Do not invent a different UI pattern for every page.

---

# 88. Claude Code — Business Logic

Before placing business logic in Livewire:

Ask:

> Is this actually a business operation?

If yes, consider an Action.

If it is reusable infrastructure, consider a Service.

If it is asynchronous, consider a Job.

If it is authorization, use a Policy.

If it is UI state, keep it in Livewire.

---

# 89. Claude Code — Verification

After meaningful implementation, Claude Code should run appropriate checks.

At minimum where applicable:

```text
Tests
Pint
Static analysis
```

Do not claim a feature is complete without verifying the relevant code.

---

# 90. Git Commits

Commits should represent logical changes.

Good:

```text
feat: add customer management
fix: prevent duplicate booking
refactor: extract invoice generation action
test: cover customer authorization
```

Avoid:

```text
changes
update
stuff
final
```

---

# 91. Pull Request Philosophy

A pull request should be:

- Focused
- Understandable
- Testable
- Reviewable

Avoid combining unrelated features into one large change.

---

# 92. Documentation

Document:

- Non-obvious architecture
- Important business rules
- External integrations
- Special deployment requirements
- Unusual technical decisions

Do not document obvious Laravel behavior merely for the sake of documentation.

---

# 93. Decision Hierarchy

When multiple solutions are possible, use this priority:

```text
1. Laravel native
2. Existing 8bit convention
3. Existing installed package
4. Small custom implementation
5. New third-party package
```

The goal is to minimize unnecessary dependencies.

---

# 94. Final Convention

Every developer and AI agent working on an 8bit application should be able to answer:

> Where does this code belong?

Use:

```text
UI state              → Livewire
Presentation           → Blade
Validation             → Form / Form Request
Authorization          → Policy / Permission
Business operation     → Action
Reusable infrastructure→ Service
Async processing       → Job
Application event      → Event
Event reaction         → Listener
Persistent data        → Model
Finite state           → Enum
Structured data        → Data object
External API            → Service
Configuration          → Config
Database settings      → Settings
```

If the answer is unclear, stop and inspect the architecture before creating a new abstraction.

---

# 95. Architecture Goal

The finished application should remain recognizable as Laravel.

The 8bit Starter exists to accelerate development, not obscure the framework.

The ideal result is:

```text
Laravel
    +
Livewire
    +
8bit conventions
    +
High-value packages
    +
Consistent UI
    +
Good tests
    +
Good developer tooling
```

This is the standard expected for applications built by 8bit Technologies.