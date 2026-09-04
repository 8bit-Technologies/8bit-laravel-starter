# 8bit Laravel Starter v1
## UI Design System

**Organization:** 8bit Technologies  
**Starter:** 8bit Laravel Starter  
**UI Stack:** Livewire 4 + Mary UI 2 + Tailwind CSS 4 + daisyUI 5  
**Design Philosophy:** Clean, modern, practical, responsive

---

# 1. Purpose

The 8bit Laravel Starter UI system provides a consistent visual and interaction foundation for applications developed by 8bit Technologies.

The goal is not to create a rigid design system that prevents product-specific design.

The goal is to establish:

- A consistent application shell
- Consistent navigation
- Consistent typography
- Consistent spacing
- Consistent forms
- Consistent tables
- Consistent dialogs
- Consistent notifications
- Consistent status indicators
- Consistent responsive behavior
- Consistent dark mode
- Consistent accessibility

Individual applications may extend the system where necessary.

---

# 2. Design Philosophy

The default 8bit interface should feel:

```text
Modern
Clean
Professional
Fast
Approachable
Information-dense without feeling crowded
```

Avoid:

```text
Overly decorative dashboards
Excessive gradients
Unnecessary animations
Huge typography
Excessive rounded cards
Visual clutter
Inconsistent spacing
```

The UI should prioritize **clarity and productivity** over decoration.

---

# 3. Design Principle

> The interface should disappear behind the user's work.

Users should quickly understand:

1. Where they are.
2. What they can do.
3. What requires attention.
4. What happened.
5. What they should do next.

---

# 4. Application Shell

The default authenticated application layout consists of:

```text
┌─────────────────────────────────────────────────────┐
│ Sidebar │ Top Navigation                            │
│         ├───────────────────────────────────────────┤
│         │                                           │
│         │ Page Header                               │
│         │                                           │
│         │ Main Content                              │
│         │                                           │
│         │                                           │
│         │                                           │
│         └───────────────────────────────────────────┤
│         │ Optional Footer                           │
└─────────────────────────────────────────────────────┘
```

---

# 5. Sidebar

The sidebar is the primary navigation mechanism for desktop users.

It should contain:

```text
Logo / Application Name

Dashboard

Main navigation
    Customers
    Bookings
    Inventory
    Orders
    Reports

Administration
    Users
    Roles & Permissions
    Settings
```

Actual items depend on the application.

---

# 6. Sidebar Rules

Navigation items should:

- Have meaningful labels.
- Use recognizable icons.
- Indicate the current section.
- Support nested navigation where required.
- Respect user permissions.
- Remain usable on smaller screens.

Do not hide important navigation inside unnecessary nested menus.

---

# 7. Sidebar Active State

The active navigation item should be visually obvious.

It should communicate:

> You are currently here.

Use the application's semantic primary color rather than inventing a separate active-state color.

---

# 8. Sidebar Collapse

Desktop applications should support a collapsed sidebar when practical.

Expanded:

```text
[Icon] Customers
[Icon] Bookings
[Icon] Reports
```

Collapsed:

```text
[Icon]
[Icon]
[Icon]
```

Tooltips should explain icons when the sidebar is collapsed.

---

# 9. Mobile Navigation

On mobile:

```text
Sidebar
   ↓
Drawer
```

The navigation should not consume permanent screen width.

Use an accessible drawer interaction.

---

# 10. Top Navigation

The top navigation should generally contain:

```text
Breadcrumb / Page context

                    Search
                    Notifications
                    User Menu
```

Not every application requires every item.

---

# 11. User Menu

The user menu may contain:

```text
Profile
Preferences
Settings
Help
Logout
```

Application-specific actions may be added.

Logout must use the application's normal authentication flow.

---

# 12. Breadcrumbs

Use breadcrumbs when the navigation hierarchy is meaningful.

Example:

```text
Bookings / 2026 / BK-1024
```

Do not use breadcrumbs merely because they exist.

Simple pages can rely on the page title.

---

# 13. Page Layout

Default page structure:

```text
Page container
    ↓
Page header
    ↓
Toolbar / Actions
    ↓
Main content
```

Example:

```text
Customers

Manage customer records.

[Import] [Export] [+ Add Customer]

[Search........................] [Status ▼]

[Customer Table]
```

---

# 14. Page Width

Use a sensible maximum content width.

Data-heavy screens may use wider layouts.

Forms and focused workflows may use narrower layouts.

Do not force every page into the same maximum width.

---

# 15. Page Header

Every major page should have a clear title.

Example:

```text
Customers
Manage customer records and contact information.
```

The page header may contain:

- Title
- Description
- Breadcrumbs
- Primary action
- Secondary actions

---

# 16. Primary Action

Every page should have a visually obvious primary action when one exists.

Examples:

```text
+ Add Customer
+ Create Booking
+ New Invoice
Generate Report
```

Avoid having multiple equally prominent primary actions unless the workflow genuinely requires it.

---

# 17. Action Hierarchy

Use:

```text
Primary
Secondary
Tertiary
Destructive
```

Example:

```text
[Save]              Primary
[Cancel]            Secondary
[View History]      Tertiary
[Delete]            Destructive
```

---

# 18. Buttons

Buttons should use action-oriented labels.

Prefer:

```text
Save Customer
Create Booking
Generate Invoice
Delete Customer
```

over:

```text
Submit
Process
Click
Continue
```

when the more specific label is possible.

---

# 19. Button Loading States

Buttons performing Livewire operations should indicate loading.

Example:

```text
Saving...
Deleting...
Generating...
```

Prevent duplicate submissions during loading.

---

# 20. Cards

Cards should be used to group meaningful information.

Good use cases:

```text
Statistics
Summary information
Related information
Forms
Dashboard widgets
```

Avoid wrapping every individual element inside a card.

---

# 21. Dashboard Cards

Statistic cards should contain:

```text
Label
Value
Optional trend/change
Optional supporting context
```

Example:

```text
Total Bookings

1,284

↑ 12.4% from last month
```

Keep dashboard cards concise.

---

# 22. Dashboard Design

The dashboard should answer:

> What does the user need to know right now?

Possible sections:

```text
Key statistics
Recent activity
Pending actions
Charts
Upcoming events
Alerts
```

Do not create charts merely to make the dashboard look impressive.

---

# 23. Tables

Tables are the default pattern for structured administrative data.

Typical structure:

```text
Toolbar
    ↓
Filters
    ↓
Table
    ↓
Pagination
```

---

# 24. Table Rules

Tables should:

- Have clear column headings.
- Use consistent alignment.
- Avoid unnecessary columns.
- Support responsive behavior.
- Provide useful empty states.
- Provide pagination for large datasets.
- Provide row actions consistently.

---

# 25. Table Actions

For row actions, prefer:

```text
View
Edit
More
```

Destructive operations should be visually separated.

Avoid displaying five or six large buttons in every row.

Use a contextual menu when appropriate.

---

# 26. Table Responsive Behavior

On smaller screens, tables should not simply overflow the entire application.

Possible strategies:

```text
Horizontal scroll
Responsive column hiding
Card/list transformation
Prioritized columns
```

Choose based on the data.

---

# 27. Table Empty State

Example:

```text
No customers found.

Try changing your filters or create a new customer.

[+ Add Customer]
```

If filters are active:

```text
No customers match your filters.

[Clear Filters]
```

---

# 28. Forms

Forms should be easy to scan.

Recommended structure:

```text
Section heading

Field
Label
Input
Help text
Validation error
```

Example:

```text
Customer Information

Name *
[................................]

Email *
[................................]
We'll use this address for notifications.

Phone
[................................]
```

---

# 29. Form Labels

Labels must always clearly identify their associated field.

Do not rely exclusively on placeholder text.

---

# 30. Required Fields

Required fields should be visually identifiable.

Use a consistent convention throughout the application.

Do not mark every field with unnecessary visual noise.

---

# 31. Placeholder Text

Placeholders should provide examples when useful.

Good:

```text
john@example.com
```

Avoid using placeholders as the only field label.

---

# 32. Help Text

Use help text when the user might reasonably have a question.

Example:

```text
Slug
[................................]

Used in the public URL.
```

Avoid explaining obvious fields.

---

# 33. Validation Errors

Validation errors should appear close to the relevant field.

Example:

```text
Email *

[invalid@email]

Please enter a valid email address.
```

Do not make users hunt through the page to find errors.

---

# 34. Form Submission

A form should provide clear feedback after submission.

Success:

```text
Customer created successfully.
```

Failure:

```text
Unable to create the customer. Please correct the highlighted fields.
```

---

# 35. Long Forms

Long forms should be divided into logical sections.

Example:

```text
Customer Information

Contact Information

Address

Billing Information

Additional Information
```

Avoid one enormous uninterrupted form.

---

# 36. Destructive Actions

Destructive actions require additional friction when accidental activation could cause harm.

Example:

```text
Delete Customer?

This will permanently remove the customer record.

[Cancel] [Delete Customer]
```

---

# 37. Confirmation Dialogs

Use confirmation dialogs for:

- Permanent deletion
- Irreversible operations
- Important state changes
- Potentially destructive bulk operations

Do not require confirmation for every ordinary action.

---

# 38. Modal Guidelines

Use modals for focused tasks.

Good:

```text
Quick edit
Confirmation
Short form
Small selection
```

Avoid putting large workflows inside a modal.

For complex workflows, prefer a dedicated page or drawer.

---

# 39. Drawers

Drawers are useful for:

- Quick details
- Filters
- Secondary information
- Short forms
- Activity history

They should not become a hidden second application screen.

---

# 40. Notifications

Use the 8bit notification system.

Notification types:

```text
Success
Info
Warning
Error
```

Messages should be short and actionable.

---

# 41. Notification Examples

Success:

```text
Booking confirmed successfully.
```

Warning:

```text
This booking has an outstanding payment.
```

Error:

```text
Unable to save the booking.
```

Info:

```text
The report is being generated.
```

---

# 42. Notification Position

Use a consistent global notification position.

The implementation should remain accessible and should not obscure important controls.

---

# 43. Status System

Statuses should use semantic visual indicators.

Examples:

```text
Pending
Confirmed
Completed
Cancelled
Active
Inactive
Draft
Published
Failed
```

Use centralized status mappings.

---

# 44. Status Badge

Repeated statuses should use the shared 8bit status component.

Example:

```text
[Confirmed]
[Pending]
[Cancelled]
```

Do not manually recreate status styling throughout the application.

---

# 45. Color Semantics

Colors should communicate meaning consistently.

Conceptually:

```text
Primary
    Main application action

Success
    Positive/completed state

Warning
    Attention required

Error
    Failure/destructive state

Info
    Neutral information

Neutral
    Secondary content
```

Do not use semantic colors merely for decoration.

---

# 46. Brand Color

The starter should support configurable branding rather than hard-coding 8bit Technologies branding into every application.

The starter may define sensible default theme tokens.

Individual applications can override:

```text
Primary color
Secondary color
Accent color
Logo
Favicon
Application name
```

---

# 47. Theme Architecture

Branding should be centralized.

Avoid hard-coding brand colors across dozens of Blade files.

Prefer theme/configuration tokens.

---

# 48. Typography

The typography system should prioritize readability.

Recommended hierarchy:

```text
Page title
Section title
Card title
Body
Secondary text
Caption
```

Avoid excessive font-weight changes.

---

# 49. Page Title

Page titles should be visually prominent without dominating the screen.

Example:

```text
Customers
```

The title should clearly establish the current context.

---

# 50. Section Titles

Use section titles to divide complex screens.

Example:

```text
Customer Information

...

Billing Information

...
```

---

# 51. Secondary Text

Use muted text for:

- Descriptions
- Metadata
- Supporting information
- Timestamps

Do not make important information too faint.

---

# 52. Spacing

Use Tailwind's spacing system consistently.

Prefer predictable spacing relationships.

Example:

```text
Page sections
    larger spacing

Form fields
    medium spacing

Label → input
    small spacing
```

Avoid arbitrary one-off spacing values unless genuinely necessary.

---

# 53. Border Radius

Use a restrained radius system.

Do not make every UI element excessively rounded.

Cards, inputs, buttons and dialogs should feel like part of the same system.

---

# 54. Shadows

Use shadows sparingly.

Good uses:

```text
Dialogs
Dropdowns
Floating elements
Elevated navigation
```

Most normal content should rely on spacing and borders rather than heavy shadows.

---

# 55. Icons

Use a consistent icon library supported by the chosen UI stack.

Icons should:

- Have recognizable meaning.
- Be visually consistent.
- Not replace labels when meaning isn't obvious.
- Include accessible labeling when needed.

Do not mix unrelated icon styles.

---

# 56. Icon-Only Buttons

Icon-only buttons should be used when the action is widely recognizable.

Examples:

```text
Edit
Delete
Close
Search
More
```

Provide accessible labels/tooltips.

---

# 57. Search

Search should be visually discoverable.

For large datasets:

```text
Search
[ Search customers... ]
```

Search should be debounced where appropriate.

Do not perform an expensive query on every keystroke without debouncing.

---

# 58. Filters

Filters should be visually grouped.

Example:

```text
[Search] [Status] [Date] [Assigned To]

[Clear Filters]
```

Active filters should be understandable.

---

# 59. Bulk Actions

For tables supporting bulk operations:

```text
☐ Select all

3 selected

[Archive] [Export] [Delete]
```

Bulk destructive actions require appropriate confirmation.

---

# 60. Pagination

Pagination should be simple and predictable.

Example:

```text
Showing 1–25 of 384

← Previous   1 2 3 4 ... 16   Next →
```

Do not overcomplicate pagination.

---

# 61. Loading UI

Use Livewire loading states appropriately.

Possible patterns:

```text
Button spinner
Inline loading
Skeleton
Table loading state
Disabled interaction
```

Avoid replacing the entire page with a spinner for small operations.

---

# 62. Skeleton Loading

Skeletons are appropriate when loading larger UI structures.

They should resemble the final content layout.

Do not use skeleton loading merely because it looks modern.

---

# 63. Empty States

Every major data-driven page must consider the empty state.

Empty states should answer:

```text
What is empty?
Why is it empty?
What can I do next?
```

---

# 64. Error States

Pages should have useful error states.

Example:

```text
Unable to load customers.

Please try again.

[Retry]
```

Do not expose raw exception messages to normal users.

---

# 65. Offline / Network Errors

Where relevant, external integrations should communicate temporary failures clearly.

Example:

```text
We couldn't connect to the payment provider.

Please try again.
```

Do not imply that a failed operation succeeded.

---

# 66. Dark Mode

Dark mode is part of the base design system.

All shared components must support it.

Avoid hard-coded colors that become unreadable in dark mode.

---

# 67. Dark Mode Principles

Dark mode should not simply invert the interface.

Maintain:

```text
Hierarchy
Contrast
Readability
Semantic colors
Focus states
Disabled states
```

---

# 68. Accessibility

The UI must support:

```text
Keyboard navigation
Visible focus
Screen readers
Form labels
Error identification
Accessible dialogs
Accessible menus
Meaningful button labels
```

---

# 69. Focus States

Interactive elements must have a visible focus state.

Never remove focus indicators simply because they don't match the design.

---

# 70. Keyboard Navigation

Users should be able to navigate:

```text
Navigation
Buttons
Forms
Dialogs
Menus
Tables
```

using a keyboard.

---

# 71. Responsive Breakpoints

Use Tailwind's responsive system.

Do not create arbitrary custom breakpoints unless the application genuinely requires them.

---

# 72. Mobile Priority

On mobile:

```text
Content
    >
Navigation chrome
```

Do not waste the majority of the screen on branding or navigation.

---

# 73. Mobile Forms

Forms should generally become one-column layouts on small screens.

Avoid forcing multiple narrow columns on mobile.

---

# 74. Mobile Tables

Prioritize the most important information.

If a table contains many columns, consider:

```text
Horizontal scrolling
Responsive cards
Hidden secondary columns
Details drawer
```

---

# 75. Mobile Actions

Primary actions should remain easy to reach.

For important workflows, consider a mobile-friendly action arrangement rather than simply shrinking desktop controls.

---

# 76. Animations

Animations should be subtle.

Use animation for:

- Opening/closing
- State transitions
- Feedback
- Loading

Avoid:

- Excessive movement
- Decorative animation
- Long transitions
- Animations that delay user interaction

---

# 77. Reduced Motion

Respect users who prefer reduced motion.

Do not make essential functionality dependent on animation.

---

# 78. Dashboard Charts

Charts should be used only when they improve understanding.

Each chart should have:

```text
Clear title
Useful labels
Understandable units
Meaningful time period
Accessible fallback/context
```

---

# 79. Data Visualization

Avoid misleading visualizations.

Do not manipulate axes or scales in ways that distort interpretation.

Use tables or summary values alongside important charts when appropriate.

---

# 80. Dates and Times

Display dates consistently throughout the application.

Prefer human-readable formats for normal users.

Example:

```text
5 Sep 2026
```

For date/time values:

```text
5 Sep 2026, 10:30 AM
```

Application-specific localization may override this.

---

# 81. Currency

Currency display should respect the application's configured currency.

Do not hard-code:

```text
₹
$
€
```

inside reusable components.

---

# 82. Number Formatting

Use human-readable formatting where appropriate.

Example:

```text
1,284
```

rather than:

```text
1284
```

For large values:

```text
12.4K
```

may be appropriate in dashboard contexts.

---

# 83. User Feedback

Every user action should have a clear outcome.

The system should communicate:

```text
Success
Failure
Loading
Validation
Confirmation
```

when appropriate.

---

# 84. Accessibility vs Visual Design

When visual design conflicts with accessibility:

> Accessibility wins.

Do not reduce contrast, remove focus indicators, or hide labels merely for visual cleanliness.

---

# 85. Component Reuse

Use shared components for repeated patterns.

Examples:

```text
8bit Page Header
8bit Status Badge
8bit Empty State
8bit Stat Card
8bit Confirm Dialog
8bit Data Table patterns
```

Do not duplicate identical UI patterns across multiple pages.

---

# 86. Component Boundaries

Create a component when it has:

```text
Repeated usage
AND
Meaningful shared behavior
OR
Meaningful shared visual identity
```

Avoid excessive component fragmentation.

---

# 87. Design Tokens

Where practical, centralize:

```text
Colors
Spacing
Typography
Radius
Shadows
Semantic states
```

The exact implementation should use Tailwind/daisyUI capabilities rather than creating a parallel CSS framework.

---

# 88. Custom CSS

Prefer Tailwind utilities and existing component styles.

Custom CSS is allowed when:

- Tailwind cannot express the required behavior cleanly.
- A reusable design pattern needs centralized styling.
- Third-party integration requires it.

Do not create large custom CSS systems unnecessarily.

---

# 89. Avoid Inline Style

Avoid:

```html
style="color: red"
```

Use the established theme and utility system.

---

# 90. Avoid Arbitrary Values

Avoid excessive use of:

```text
w-[437px]
mt-[13px]
text-[17px]
```

Prefer the standard design scale.

Arbitrary values are acceptable when the design genuinely requires a specific value.

---

# 91. Application Branding

The starter should provide configurable:

```text
Application name
Logo
Favicon
Primary theme
Secondary theme
Login branding
Footer branding
```

The application should not need to modify dozens of templates to change branding.

---

# 92. Authentication Screens

Authentication screens should follow the same visual language as the main application.

Typical screens:

```text
Login
Register
Forgot Password
Reset Password
Verify Email
Two-Factor Authentication
```

They should remain simple and focused.

---

# 93. Login Page

The login page should prioritize:

```text
Logo
Application name
Login form
Primary action
Password recovery
Optional registration
```

Avoid unnecessary marketing content unless the application requires it.

---

# 94. Profile Page

The profile area may contain:

```text
Personal information
Password
Two-factor authentication
Sessions
Preferences
```

Only expose functionality implemented by the application.

---

# 95. Settings

Application settings should use a consistent settings navigation.

Example:

```text
Settings

General
Appearance
Notifications
Security
Integrations
```

Group related configuration logically.

---

# 96. Administration

Administrative interfaces may be more information-dense than user-facing interfaces, but should retain the same visual system.

Typical areas:

```text
Users
Roles
Permissions
System settings
Activity
Logs
```

---

# 97. Permission-Aware UI

If a user lacks permission:

- Hide actions they cannot perform when appropriate.
- Disable actions only when there is value in explaining why.
- Always enforce authorization server-side.

---

# 98. Bulk Data Interfaces

Large administrative datasets should prioritize:

```text
Search
Filters
Pagination
Bulk actions
Clear selection
Export
```

Do not overload users with unnecessary controls.

---

# 99. Accessibility of Dynamic Interfaces

Livewire-driven updates must remain understandable to assistive technologies.

Where appropriate, provide:

- Live regions
- Status messages
- Focus management
- Accessible dialog behavior

---

# 100. Final UI Principle

The 8bit Laravel Starter UI should make every application feel like it belongs to the same ecosystem.

The visual language should communicate:

```text
8bit Technologies
        ↓
Professional
        ↓
Simple
        ↓
Fast
        ↓
Consistent
        ↓
Scalable
```

The starter should provide strong defaults while remaining easy to brand for each individual client/project.

---

# 101. Claude Code UI Rules

When Claude Code builds a UI feature:

1. Inspect existing UI components first.
2. Reuse Mary UI where appropriate.
3. Reuse existing 8bit components.
4. Follow the established spacing and typography.
5. Follow semantic color conventions.
6. Implement responsive behavior.
7. Consider dark mode.
8. Consider accessibility.
9. Add loading states where appropriate.
10. Add empty/error states where appropriate.
11. Do not introduce another UI framework.
12. Do not invent a new design language.

---

# 102. UI Review Checklist

Before considering a UI feature complete:

```text
[ ] Page hierarchy is clear
[ ] Primary action is obvious
[ ] Existing components reused
[ ] Responsive layout works
[ ] Mobile behavior considered
[ ] Dark mode works
[ ] Loading state considered
[ ] Empty state considered
[ ] Error state considered
[ ] Validation errors are clear
[ ] Destructive actions are protected
[ ] Keyboard navigation works
[ ] Focus states are visible
[ ] Labels are accessible
[ ] No unnecessary custom CSS
[ ] No unnecessary dependencies
[ ] Visual style matches the starter
```

---

# 103. Design System Rule

When a new UI pattern appears repeatedly across projects:

```text
Application A
       ↓
Application B
       ↓
Application C
       ↓
Repeated pattern
       ↓
Evaluate
       ↓
Add to 8bit UI system
```

The design system should evolve from real-world usage rather than speculation.

---

# 104. Final Standard

A new developer should be able to open an unfamiliar 8bit application and immediately understand:

```text
Where am I?
What can I do?
What happened?
What needs attention?
Where are the settings?
Where is my account?
```

That is the standard for the 8bit Laravel Starter UI.