# 8bit Laravel Starter v1
## Feature Set & Module Specification

**Organization:** 8bit Technologies  
**Starter:** 8bit Laravel Starter  
**Minimum PHP:** 8.3  
**Laravel:** 13  
**Livewire:** 4  
**UI:** Mary UI 2 + Tailwind CSS 4 + daisyUI 5

---

# 1. Purpose

This document defines what functionality belongs in the 8bit Laravel Starter and what should remain optional.

The starter is intended to provide a strong foundation for commercial Laravel applications without becoming a large application that must be stripped down for every new project.

---

# 2. Core Principle

The starter follows:

> **Build the foundation, not the finished application.**

Everything included should satisfy at least one of these conditions:

1. It is useful in almost every application.
2. It establishes an important architectural convention.
3. It removes repetitive setup work.
4. It provides security or operational infrastructure.
5. It provides a reusable UI pattern.

Everything else should remain optional.

---

# 3. Module Classification

Every feature belongs to one of three categories:

```text id="k2n0f4"
CORE
    Installed and available by default.

OPTIONAL
    Documented and easy to add.

APPLICATION
    Must be implemented by the individual project.
```

---

# 4. Core Modules

The base starter should contain:

```text id="wh4y17"
Authentication
User Management
Roles & Permissions
Application Settings
Profile
Dashboard Shell
Notifications
Media Infrastructure
Activity Logging
Health Checks
Error Handling
Basic Administration
```

---

# 5. Authentication

Authentication is part of the base starter.

The exact Laravel authentication starter implementation should be chosen based on current Laravel conventions and Livewire compatibility.

Required functionality:

```text id="22byqx"
Login
Logout
Password Reset
Email Verification
Registration
```

Additional authentication features may be enabled depending on application requirements.

---

# 6. User Management

The starter should include a basic administrative user management module.

Capabilities:

```text id="6w6eqk"
List users
Search users
View user
Create user
Edit user
Deactivate user
Delete user where appropriate
Assign roles
```

Do not attempt to build a complete enterprise IAM platform.

---

# 7. User Status

Users should have a simple status model.

Recommended:

```text id="9grf9c"
Active
Inactive
```

Do not create unnecessary user states unless required.

---

# 8. Roles & Permissions

Spatie Permission is part of the core stack.

The starter should provide:

```text id="4c99vh"
Roles
Permissions
Role assignment
Permission assignment
Authorization helpers
Policy integration
```

---

# 9. Default Roles

The starter should not assume that every application needs exactly the same roles.

A minimal default role strategy may be:

```text id="h8f76p"
Super Admin
```

Additional roles should be created by the application.

Avoid creating:

```text id="fdw6pv"
Admin
Manager
Editor
Staff
Accountant
Operator
...
```

unless the actual application needs them.

---

# 10. Super Admin

The starter should support a super-admin concept where appropriate.

Super admin privileges must be implemented through the established authorization architecture.

Do not scatter:

```php id="r8g8xj"
if ($user->is_admin)
```

through application code.

---

# 11. Policies

Policies remain the primary mechanism for resource authorization.

Permissions complement policies rather than replacing them.

Conceptually:

```text id="t1if4p"
User
  ↓
Permission / Gate
  ↓
Policy
  ↓
Resource operation
```

---

# 12. Dashboard Shell

The starter should include a functional dashboard shell.

It should demonstrate:

```text id="gjh24r"
Sidebar
Topbar
Page header
Content container
Responsive navigation
Notifications
User menu
```

The dashboard itself should remain intentionally generic.

---

# 13. Dashboard Content

The base starter should avoid pretending to know the application's business metrics.

Therefore the default dashboard should contain only useful system-level information, such as:

```text id="7g8r1n"
Welcome / overview
Current user information
Recent activity
System status
Quick links
```

Application-specific dashboards should replace or extend this.

---

# 14. Profile

The starter should provide a profile area.

Minimum:

```text id="b3av41"
Name
Email
Password
Profile information
```

Where supported:

```text id="y98k37"
Profile photo
Two-factor authentication
Active sessions
```

---

# 15. Application Settings

The starter should provide centralized application settings infrastructure.

Potential settings:

```text id="h8ld1n"
Application name
Logo
Favicon
Timezone
Locale
Date format
Currency
Branding
```

Applications may extend this.

---

# 16. Settings Architecture

Settings should be grouped logically.

Example:

```text id="g8h3ec"
General
Appearance
Localization
Notifications
Integrations
Security
```

Do not create a settings page containing dozens of unrelated fields.

---

# 17. Notifications

The starter should provide a standard notification mechanism.

Support:

```text id="by2f8h"
Success
Info
Warning
Error
```

Notifications should work naturally with Livewire.

---

# 18. Notification Architecture

The application should have a simple developer-facing API.

Conceptually:

```php id="3uvkxm"
$this->notifySuccess('Saved successfully.');
$this->notifyError('Something went wrong.');
```

The underlying implementation should remain centralized.

Do not couple business Actions directly to a specific frontend notification library.

---

# 19. Media Library

Spatie Media Library is part of the core infrastructure.

Use it for:

```text id="5x6v6s"
Images
Documents
Attachments
User avatars
Application logos
```

The starter should establish sane defaults without attempting to predict every media requirement.

---

# 20. Media Collections

Do not create dozens of default media collections.

The starter may define only genuinely universal collections, such as:

```text id="m0ex90"
avatar
```

Applications define their own:

```text id="v7rrzo"
gallery
documents
attachments
logo
```

as required.

---

# 21. Media Validation

Media uploads should support validation for:

```text id="e5zq1a"
MIME/type
Size
Dimensions when appropriate
```

Validation rules should be application-specific where requirements differ.

---

# 22. Activity Logging

Activity logging is recommended as a core feature because it is useful across many commercial applications.

The implementation should use the chosen Laravel-compatible activity-log package/convention rather than creating custom logging infrastructure.

Typical events:

```text id="d5y6sq"
User created
User updated
Role changed
Important resource created
Important resource updated
Important resource deleted
```

Do not log every database query or trivial UI interaction.

---

# 23. Activity Log UI

The starter may provide a basic activity log interface.

Possible features:

```text id="6q4ql5"
Date
User
Event
Subject
Description
```

Detailed filtering can be added when required.

---

# 24. Health Checks

The starter should include basic application health monitoring.

Possible checks:

```text id="s0h1as"
Application
Database
Cache
Queue
Storage
```

Do not expose sensitive diagnostic information publicly.

---

# 25. Debugging

Development environments should include Laravel Debugbar.

Debugbar must not be enabled in production.

---

# 26. Developer Tools

The starter should be developer-friendly.

Recommended development tooling:

```text id="51rjpd"
Laravel Boost
Debugbar
Pest
Pint
Static analysis
```

These tools should not unnecessarily affect production performance.

---

# 27. Laravel Boost

Laravel Boost is part of the development workflow.

Claude Code should use Boost capabilities where appropriate when understanding or working with the Laravel application.

Boost does not replace normal project documentation or code inspection.

---

# 28. Search

Global search should **not** be included as a fully implemented feature in the base starter.

However, the starter should establish conventions that make adding search straightforward.

Each application can decide whether it needs:

```text id="l8s47g"
Resource search
Global search
Full-text search
External search engine
```

---

# 29. File Manager

A complete file manager should **not** be part of the base starter.

Spatie Media Library provides the underlying media infrastructure.

Applications requiring a file manager can add one.

---

# 30. Audit vs Activity

Do not confuse activity logging with a legally significant audit system.

If an application requires:

```text id="glh8hf"
Immutable audit records
Compliance retention
IP tracking
Before/after values
Legal audit trails
```

that should be designed as a dedicated application requirement.

---

# 31. API

A full API should **not** automatically be included.

The starter may retain Laravel API capabilities but should not generate dozens of unused controllers/resources.

Applications should decide whether they require:

```text id="7w9w9x"
REST API
Webhook API
Mobile API
Third-party integration API
```

---

# 32. API Authentication

When an application needs API authentication, choose the appropriate Laravel-supported mechanism.

Do not install API authentication packages preemptively.

---

# 33. Queues

Queue infrastructure should be supported by the starter.

The starter should establish sensible conventions for Jobs.

Do not require every application to configure a complex queue system if it does not need one.

---

# 34. Scheduler

Laravel Scheduler should remain available.

Applications can define scheduled tasks as needed.

Do not create placeholder scheduled commands that do nothing.

---

# 35. Cache

Use Laravel's cache abstraction.

Do not build a custom cache layer.

---

# 36. Events & Listeners

The starter should support Laravel Events and Listeners naturally.

Do not pre-register speculative events.

---

# 37. Mail

Mail infrastructure remains available through Laravel.

Do not create a custom mail abstraction unless multiple projects demonstrate the need.

---

# 38. Storage

Use Laravel Filesystem.

Media-specific storage should use Spatie Media Library.

Do not create a second storage abstraction.

---

# 39. Logging

Use Laravel's logging system.

Logs should be structured enough to diagnose production issues.

Never log:

```text id="f1iy31"
Passwords
API keys
Authentication tokens
Private secrets
Sensitive personal data unnecessarily
```

---

# 40. Error Pages

The starter should provide clean user-facing error pages where appropriate.

Examples:

```text id="khm1ck"
403
404
419
429
500
503
```

They should follow the 8bit visual system.

---

# 41. Authorization Errors

A user who lacks permission should receive an appropriate response.

Do not expose implementation details such as:

```text id="x8oyj0"
Policy class names
Database IDs
Internal exceptions
```

---

# 42. 404 Handling

The application should provide a clear not-found experience.

Example:

```text id="2wjg0k"
Page not found.

The page you're looking for doesn't exist or may have been moved.

[Go to Dashboard]
```

---

# 43. Maintenance Mode

Laravel maintenance mode should be supported.

The UI should provide an appropriate maintenance page.

---

# 44. Health & Monitoring

The starter should remain compatible with external monitoring systems.

Do not attempt to build a complete monitoring platform.

---

# 45. SEO

A full SEO management system should **not** be part of the base starter.

However, applications may benefit from basic SEO conventions for public pages.

Optional SEO infrastructure can be added later.

---

# 46. Social Media

Social media publishing should **not** be part of the base Laravel starter.

It belongs in an optional marketing/social module or a dedicated product.

This keeps the starter suitable for:

```text id="jv4rj6"
ERP
CRM
Booking systems
Inventory
Accounting
Internal tools
SaaS products
Client portals
```

---

# 47. Payments

Payment functionality should remain optional.

Do not include Stripe/Razorpay/etc. in the core starter.

Applications should install the required gateway.

---

# 48. Notifications Beyond UI

Email/SMS/WhatsApp notifications should remain optional integrations.

The base starter should not assume a communication provider.

---

# 49. Localization

The starter should be localization-ready.

Support should exist for:

```text id="y2p4sq"
Application locale
Timezone
Date formatting
Number formatting
Currency formatting
```

The base application does not need to ship translations for every language.

---

# 50. Multi-Tenancy

Multi-tenancy should **not** be included in the base starter.

It materially changes application architecture.

When required, choose the appropriate tenancy architecture for the product.

---

# 51. Billing & Subscriptions

Billing should remain optional.

Do not install subscription/billing infrastructure into every application.

---

# 52. Two-Factor Authentication

Two-factor authentication is highly recommended but should be treated according to the chosen authentication starter.

The implementation must follow Laravel-compatible security practices.

---

# 53. Impersonation

User impersonation should remain optional.

It is useful for:

```text id="r7w9v8"
Support teams
SaaS administration
Customer troubleshooting
```

but should not be enabled by default without appropriate security controls.

---

# 54. API Documentation

Do not include Swagger/OpenAPI tooling by default.

Add it when the application exposes an API.

---

# 55. Import / Export

Generic import/export infrastructure should not be included by default.

Applications can add:

```text id="0rj1kp"
CSV import
Excel import
CSV export
Excel export
PDF export
```

as required.

---

# 56. Reports

A generic report engine should not be included.

The starter can provide reusable UI patterns for reports, but actual reports belong to the application.

---

# 57. PDF Generation

PDF generation should remain optional.

Only install/configure a PDF package when the application needs it.

---

# 58. Excel

Spreadsheet import/export should remain optional.

Do not install spreadsheet tooling merely because some future project may need it.

---

# 59. Search Engines

Do not include:

```text id="zj5jv8"
Elasticsearch
Meilisearch
Algolia
Typesense
```

in the base starter.

Add the appropriate search solution when required.

---

# 60. Realtime

Realtime functionality should remain optional.

Applications requiring realtime functionality may add Laravel Reverb or another appropriate solution.

Do not install realtime infrastructure unnecessarily.

---

# 61. Notifications Architecture

The starter should make it easy to add:

```text id="pld9pq"
Database notifications
Email
SMS
WhatsApp
Push
```

without coupling the application's business logic to a specific provider.

---

# 62. Optional Module Examples

Future optional modules may include:

```text id="7wq4f9"
Blog
CMS
SEO
Social Media
CRM
Bookings
Inventory
Accounting
Payments
Subscriptions
API
Import/Export
Reports
Search
Multi-tenancy
Realtime
```

These should be separate from the core starter.

---

# 63. Starter Directory Philosophy

The base project should not contain empty directories for every possible module.

Create directories when the corresponding functionality exists.

Avoid:

```text id="oqcb1s"
app/
├── Billing/
├── CRM/
├── Inventory/
├── SEO/
├── Social/
├── Reports/
├── CMS/
└── ...
```

when none of those features are implemented.

---

# 64. Base Application Navigation

The starter navigation should be minimal.

Example:

```text id="f19yyq"
Dashboard

Administration
    Users
    Roles & Permissions

Settings
```

Additional application modules should register their own navigation.

---

# 65. Navigation Registration

Where practical, application modules should be able to contribute navigation without modifying a giant central navigation file.

However, do not create an elaborate plugin architecture prematurely.

A simple, understandable approach is preferred.

---

# 66. Dashboard Widgets

The starter should provide a way to add dashboard widgets without requiring modification of the entire dashboard.

Widgets may include:

```text id="s04j7d"
Stats
Charts
Recent activity
Pending actions
System information
```

---

# 67. Reusable CRUD Foundation

The starter should provide conventions rather than a generic CRUD generator.

A developer should be able to quickly build:

```text id="3zqx50"
Customers
Products
Bookings
Orders
Invoices
```

using the established patterns.

Do not create a magic CRUD system that hides Laravel.

---

# 68. Generic Table Component

A reusable table component may be included if it remains lightweight.

It should support common patterns such as:

```text id="p3f7j4"
Search
Sort
Filters
Pagination
Selection
Actions
```

It should not become a full replacement for Livewire or Eloquent.

---

# 69. Generic Form Components

Use Mary UI for standard fields.

Only create 8bit wrappers for recurring patterns.

Examples:

```text id="u4i0w5"
Date range
Money field
Address block
Status selector
```

---

# 70. Command-Line Helpers

The starter may eventually include helpful Artisan commands.

Examples:

```text id="i2e8qu"
8bit:install
8bit:setup
8bit:permissions
8bit:make-module
```

However, commands should only be added when they provide meaningful repeated value.

Do not create commands that merely wrap one existing Artisan command.

---

# 71. Installation Experience

A new project should be able to go from:

```text id="tdq7kc"
Fresh Laravel installation
        ↓
8bit Starter installation
        ↓
Environment configuration
        ↓
Database migration
        ↓
Seed essential system data
        ↓
Ready to develop
```

with minimal manual work.

---

# 72. Demo Data

The starter may provide optional demo data.

Demo data should never be confused with essential system data.

Recommended separation:

```text id="j6t5ki"
SystemSeeder
DemoSeeder
```

---

# 73. Essential Seed Data

Essential seed data may include:

```text id="l8crgk"
Required permissions
Required system roles
Essential application settings
```

It must be safe to run repeatedly.

---

# 74. Demo Credentials

If demo users are included, credentials must be clearly identified as development-only.

Never ship known demo credentials into a production deployment.

---

# 75. Starter Branding

The starter should contain 8bit Technologies branding as the default.

It must be easy to replace with client/project branding.

---

# 76. Branding Configuration

At minimum:

```text id="cz3g3b"
Application name
Logo
Favicon
Theme
```

should be configurable.

---

# 77. Login Branding

The authentication screens should automatically use the configured application branding.

Do not hard-code the application name into multiple Blade files.

---

# 78. Core Package Philosophy

The starter's core package set should remain intentionally small.

The current baseline includes:

```text id="4y7j6d"
Laravel
Livewire
Mary UI
Tailwind CSS
daisyUI
Laravel Boost
Spatie Permission
Spatie Media Library
Spatie Activitylog
Debugbar
Pest
Pint
Static analysis
```

The exact versions and package list must be maintained in `PACKAGES.md`.

---

# 79. Optional Package Philosophy

Packages should be added per application when needed.

Examples:

```text id="x1gkqp"
Payments
Search
PDF
Excel
API authentication
Realtime
Multi-tenancy
Subscriptions
Social APIs
```

Do not pollute the base starter with them.

---

# 80. Future Starter Evolution

The starter should evolve from real application experience.

A feature becomes a candidate for the core starter when:

```text id="f1oc6a"
It appears repeatedly
        AND
Its architecture is stable
        AND
It saves meaningful development time
        AND
It does not unnecessarily constrain projects
```

---

# 81. Versioning

The starter itself should be versioned.

Recommended:

```text id="8n84jr"
v1.0.0
v1.1.0
v1.2.0
v2.0.0
```

Breaking architectural changes require a major version.

---

# 82. Starter Changelog

Maintain a changelog documenting:

```text id="v7t1wu"
New features
Bug fixes
Breaking changes
Dependency changes
Upgrade notes
```

---

# 83. Starter Update Strategy

Applications created from the starter are not automatically identical copies forever.

Once a project starts development, it becomes its own application.

Common improvements can later be migrated back into the starter deliberately.

---

# 84. What the Starter Must NOT Become

Do not allow the starter to become:

```text id="7eq6mc"
A full ERP
A full CRM
A CMS
A SaaS billing platform
A marketing platform
A social media manager
A generic CRUD generator
A custom Laravel replacement
```

It is a **foundation**.

---

# 85. Final Core Feature Set

The recommended v1 starter contains:

```text id="z9a5cl"
┌─────────────────────────────────────┐
│       8bit Laravel Starter          │
├─────────────────────────────────────┤
│ Authentication                      │
│ User Management                     │
│ Roles & Permissions                 │
│ Profile                             │
│ Application Settings                │
│ Dashboard Shell                     │
│ Notification System                 │
│ Media Infrastructure                │
│ Activity Logging                    │
│ Health Checks                       │
│ Error Pages                         │
│ Responsive UI System                │
│ Dark Mode                           │
│ Developer Tooling                   │
└─────────────────────────────────────┘
```

Everything else should be **optional or application-specific**.

---

# 86. Final Principle

The best starter is not the one with the most features.

It is the one that allows a developer to start building the **actual product** immediately.

The target experience is:

```text id="ezv4v5"
Clone / Install Starter
        ↓
Configure .env
        ↓
Run migrations
        ↓
Create first admin
        ↓
Start building the product
```

No unnecessary cleanup should be required before application development begins.