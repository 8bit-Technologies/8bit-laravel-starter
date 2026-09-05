# 8bit Laravel Starter v1
## Phase 3 — Roles & Permissions: Design Specification

**Organization:** 8bit Technologies
**Starter:** 8bit Laravel Starter
**Status:** Design only. Nothing in this document is implemented yet.
**Depends on:** Spatie Laravel Permission `^8.0` (installed), Laravel authorization (Policies/Gates), Spatie Laravel Activitylog `^4.12` (installed)
**Revision note:** This revision replaces the two-layer "Gate ability backed by a dot-notation Spatie permission" design from the previous version of this document with a single-layer design approved directly from UX reference screenshots: the permission's human-readable name **is** the Gate ability, with no separate internal identifier. See Section 26 for the documentation contradiction this initially created with `ARCHITECTURE.md`/`CONVENTIONS.md` — reported at the time rather than silently resolved, and since reconciled by explicit project-owner instruction; all three documents now agree.

---

# 1. Purpose

This document is the implementation specification for Phase 3: the authorization foundation of the 8bit Laravel Starter.

It exists so that Phase 3 can be implemented deliberately, from a single agreed design, rather than discovered incrementally. It does not implement anything. No migrations, models, policies, seeders, commands, routes, or UI described here exist yet unless explicitly noted as already implemented in Phase 1/2/2.1.

This document follows the same conventions as `ARCHITECTURE.md`, `CONVENTIONS.md`, and `FEATURE-SET.md` wherever they are not directly superseded by an explicit, approved decision recorded here. Where a genuine contradiction was found, it is called out explicitly in Section 26 rather than silently resolved.

---

# 2. Scope of This Document

In scope: the authorization architecture — roles, permissions, the Super Admin concept, protection rules, naming conventions, the Roles & Permissions Manager UX, route/UI authorization boundaries, seeding strategy, and the first-Super-Admin bootstrap process.

Out of scope (see Section 25 for the full list): actually building the Roles/Permissions/Users UI, the Artisan command, seeders, migrations, or any change to existing Phase 1/2/2.1 code. Those are Phase 3 **implementation**, which follows only after this document is approved.

---

# 3. Core Authorization Principles

Two questions must never be conflated:

```text
Authentication
    Who is the user?

Authorization
    What is the user allowed to do?
```

Phase 2 established authentication (login, registration, password reset, email verification) and a route-level boundary (`auth`, `verified`) around `/dashboard`, `/profile`, and `/admin/dashboard`. That boundary answers "is this a real, verified user?" — it does not, and must not, answer "should this user see the Admin Panel?"

**Being authenticated does not imply Admin Panel access.** As of Phase 2.1, `/admin/dashboard` is reachable by any verified user purely because no authorization layer exists yet. Phase 3 closes that gap. Admin access is permission-based, established in Section 9.

---

# 4. Initial System Roles

The starter ships three roles as sensible defaults, not as an exhaustive or fixed list:

```text
Super Admin
Admin
Member
```

These are seed data, not architecture. The application must support arbitrary future roles — `Manager`, `Editor`, `Accountant`, `Hotel Manager`, `Sales`, and so on — without any code change to the authorization system itself. New roles are created through the Roles Manager (Section 12), which is generic and does not need to know role names in advance.

Consistent with `ARCHITECTURE.md` §16 ("Do not use roles directly when a policy or permission is more appropriate") and `CLAUDE.md` §28, **authorization decisions in code must check permissions, not role names**, with exactly one deliberate exception: the Super Admin concept itself (Section 5), which is inherently identity-based rather than capability-based and is treated as a distinct, protected mechanism rather than an ordinary role-based capability check.

```php
// Bad — hard-codes a role name for an ordinary capability check
if ($user->hasRole('Admin')) { ... }

// Good — checks the capability
if ($user->can('view users')) { ... }
```

---

# 5. The Super Admin Concept

Super Admin is not an ordinary role. It is a protected system concept that happens to be represented as a Spatie role for storage convenience.

A Super Admin:

```text
Bypasses normal permission checks (see Section 11)
Has complete Admin access
Can manage roles
Can manage permissions
Can create/assign another Super Admin
Can manage all administrative functionality
```

## Critical security rule

No user other than an existing Super Admin may:

```text
Create a Super Admin
Assign the Super Admin role
Remove Super Admin protection from a user
Rename a role into "Super Admin"
Convert another role into the Super Admin role
Manufacture Super Admin privileges through permission creation
Modify protected authorization configuration to obtain Super Admin privileges
Grant themselves Super Admin capability
Grant another user Super Admin capability
```

This must be enforced **server-side**, in the authorization layer itself (Policies/Gates/Actions), never by:

```text
Hiding a button
Disabling a form field
A Livewire computed property that merely hides UI
Checking the role name only in the browser/Blade template
```

A Blade `@can` or `@if($user->isSuperAdmin())` check may hide the "Promote to Super Admin" button from a normal Admin's view. That is UX, not security. The actual mutation (the Livewire action or route handler that performs the promotion) must independently re-check the same rule server-side before touching the database. The Roles & Permissions Manager (Sections 12–13) must not rely only on hiding UI controls. See Section 23.

---

# 6. Preventing Super Admin Escalation

This is the section that answers the review question in Section 26: *can a normal Admin manufacture Super Admin privileges?* The answer must be no, through every path below — including the new path introduced by allowing administrators to create their own permissions (Section 13).

The three seed role names (Section 4) are represented as a PHP enum, matching `ARCHITECTURE.md` §38 ("Use PHP enums for finite states"):

```php
// app/Enums/SystemRole.php
enum SystemRole: string
{
    case SuperAdmin = 'Super Admin';
    case Admin = 'Admin';
    case Member = 'Member';
}
```

This is deliberately an enum for the three *seed* roles only, not for the open-ended permission catalogue (Section 24) — the set of roles and permissions a project may eventually define is unbounded, but the specific, protected concept of "the Super Admin role" is not, and an enum communicates that fixedness directly in code.

## 6.1 The role name is not the security boundary by itself

Relying only on `$user->hasRole('Super Admin')` scattered through the codebase creates an escalation path: if a normal Admin can rename a role they control (e.g. an ordinary "Manager" role) to the literal string `Super Admin`, every user holding that role instantly passes any `hasRole('Super Admin')` check — without ever going through an intentional "grant Super Admin" action.

The starter closes this with three combined rules, all enforced in one centralized place (never duplicated per-feature):

**Rule 1 — Centralize the check.** Exactly one method answers "is this user a Super Admin":

```php
// app/Models/User.php
public function isSuperAdmin(): bool
{
    return $this->hasRole(SystemRole::SuperAdmin->value);
}
```

No other file performs a raw `hasRole('Super Admin')` string comparison. Every authorization decision that cares about Super Admin status calls `$user->isSuperAdmin()`.

**Rule 2 — The "Super Admin" role is immutable through the UI.** The Super Admin role, once seeded, can never be renamed or deleted by anyone through the Admin Panel — not even by a Super Admin. It is system-protected data, on the same footing as a migration: changeable by a developer with direct database/tinker access, never through application UI. This is enforced in the Roles Manager authorization boundary (Section 12), not by convention alone.

**Rule 3 — No role may be renamed *into* the protected name.** Independently of Rule 2, any attempt to update *any* role's `name` to `Super Admin` (case-insensitively) is rejected by the same authorization boundary, regardless of who performs it. This closes the "rename Manager to Super Admin" path even for someone who otherwise has full role-editing capability.

```text
Roles Manager authorization boundary
    ↓
Is the target role the protected Super Admin role?
    → YES → reject the mutation entirely (Rule 2)
    ↓ NO
Does the requested new name equal "Super Admin" (case-insensitive)?
    → YES → reject the mutation entirely (Rule 3)
    ↓ NO
Proceed with normal role-update authorization
```

## 6.2 Assigning the Super Admin role requires being a Super Admin

Assigning any role to a user is normally gated by a capability such as `update users` combined with the ability to assign roles (Section 12). Assigning the Super Admin role specifically requires the acting user to independently satisfy `$actor->isSuperAdmin()`. Holding role-assignment capability is necessary but never sufficient for this one specific assignment.

```php
// Conceptual guard inside the role-assignment Action, not a Livewire-only check
if ($role->name === SystemRole::SuperAdmin->value && ! $actor->isSuperAdmin()) {
    abort(403);
}
```

## 6.3 Permission accumulation must not reconstruct Super Admin power

A normal Admin who can edit roles and assign permissions to them could otherwise grant a role every permission in the catalogue one at a time, becoming functionally equivalent to a Super Admin without ever touching the protected role. The starter prevents this by classifying a fixed, explicit set of permissions as **protected/system permissions** (Section 13) — the exact thirteen permissions in the Initial Permission Catalogue (Section 7):

```text
access dashboard
view users, create users, update users, delete users
view roles, create roles, update roles, delete roles
view permissions, create permissions, update permissions, delete permissions
```

Because permission names are now plain human-readable strings rather than dot-notation identifiers (Section 7), this protected set **cannot** be expressed as a wildcard prefix match (there is no `users.*` to match against) — it must be an explicit, enumerated list, maintained by developers (Section 13). This is a direct, deliberate consequence of the naming convention change and is treated as a feature, not a limitation: an explicit list is unambiguous and cannot silently under- or over-match as the catalogue grows.

Only a Super Admin may attach a protected permission to a role, or grant one directly to a user. A normal Admin may freely manage **custom** permissions (anything outside the protected set — a future project's `view properties`, `create bookings`, and so on) on roles they administer, but cannot escalate a role's power into system-administration territory. This is the same "system vs custom" split described in Section 13, applied here specifically as an escalation guard.

## 6.4 Permission creation must not manufacture protected access

Section 13 approves letting administrators create new, custom permissions through the Permissions Manager (a reversal from the previous revision of this document, made to match the approved UX). This introduces a new escalation surface that Sections 6.1–6.3 do not, by themselves, close: an administrator could try to create a *new* permission that collides with, or is confusingly similar to, a protected permission's exact name, or could try to *rename* a custom permission they created into a protected permission's exact name, hoping to hijack whatever `@can(...)` / `middleware('can:...')` checks already reference that string in application code.

Both are rejected by the same authorization boundary, unconditionally, regardless of who is attempting it (Section 13 gives the precise rules):

```text
Creating a permission whose name matches a protected permission
    (case-insensitive, trimmed) → rejected. The protected permission already
    exists; a second row with the same name would be either a confusing
    duplicate or, if application code resolves permissions by name rather
    than by ID, a genuine collision.

Renaming any permission to a name that matches a protected permission
    (case-insensitive, trimmed) → rejected, for the same reason renaming a
    role into "Super Admin" is rejected (Rule 3, Section 6.1).

Renaming or deleting a protected permission itself → rejected, always,
    for everyone, matching Rule 2's treatment of the Super Admin role.
```

Creating a genuinely new, non-colliding custom permission (e.g. `view properties` for a project that has no existing permission by that name) is safe and does nothing dangerous by itself: a permission with no corresponding `$user->can(...)` check anywhere in application code grants nothing at all. The danger is specifically in colliding with or overwriting an *existing, protected* name — which is exactly what this section closes.

## 6.5 Self-service role editing cannot grant new power to the editor

A user must never be able to expand the permission set of a role **they themselves currently hold**, even for non-protected permissions, without the same protected-permission check above still applying. Combined with 6.3–6.4, this means the only way a normal Admin gains new capability is for a Super Admin (or a differently-permissioned Admin who does not hold the role being edited) to grant it.

## 6.6 Summary answers

```text
Can a normal Admin manufacture Super Admin privileges?           No — Sections 6.1–6.5 above, all server-side.
Can a normal Admin rename a role into "Super Admin"?              No — 6.1 Rule 3, enforced regardless of actor.
Can a normal Admin assign the Super Admin role?                   No — Section 6.2, isSuperAdmin() required.
Can a normal Admin manipulate permissions to self-escalate?       No — Sections 6.3–6.4, protected permissions.
Can a normal Admin create a permission to obtain protected access? No — Section 6.4, collision/rename checks.
```

---

# 7. Permission Naming Convention

**This section supersedes the dot-notation convention (`resource.action`) used in the previous revision of this document.** Permissions are now human-readable capability phrases, matching the approved Roles & Permissions Manager UX exactly:

```text
{verb} {resource}
```

Examples, matching the approved UX reference:

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

Users of the Admin Panel never need to understand a technical identifier separate from what they see: the permission's display name in the Permissions Manager, the name attached to a role in the Roles Manager, and the string used in `@can('access dashboard')` / `middleware('can:access dashboard')` are, in the common case, the exact same string (Section 9, Section 22).

## Initial Permission Catalogue

The starter establishes a minimal core set — exactly the thirteen permissions sufficient for the authorization manager to manage itself:

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

Additional business permissions such as `view properties`, `create bookings`, `cancel bookings`, `view events`, and so on are explicitly **not** hard-coded into the starter. They are examples of application-specific permissions that an individual project creates for itself, either through the Permissions Manager (Section 13) or a project-specific seeder, once that project's own features exist to check them. The same `{verb} {resource}` convention extends to them without any change to the underlying architecture (Section 24).

Every permission must correspond to a real, code-level authorization check. Do not create a permission for every UI element or button; permissions represent meaningful capabilities, matching `CLAUDE.md` §27's spirit even though the exact naming grammar has changed.

---

# 8. Permission vs Policy

Both mechanisms are used, for different questions:

```text
Permission
    "Can this user perform this capability at all?"
    Example: update users

Policy
    "Can this user perform this action on this specific model/resource?"
    Example: UserPolicy::update($actor, $targetUser)
```

A capability check alone is not enough once a specific resource is involved. `update users` tells you the acting user is allowed to edit *some* user; it does not tell you whether they should be allowed to edit *this particular* user (themselves, a Super Admin, an already-deactivated account). That contextual decision belongs in a Policy, which may itself consult permissions and the Super Admin protections from Section 6:

```php
final class UserPolicy
{
    public function update(User $actor, User $target): bool
    {
        if ($target->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            return false;
        }

        return $actor->can('update users');
    }

    public function delete(User $actor, User $target): bool
    {
        if ($target->is($actor)) {
            return false; // never delete your own account through User Management
        }

        if ($target->isSuperAdmin() && $target->isTheLastSuperAdmin()) {
            return false; // see Section 19
        }

        return $actor->can('delete users');
    }
}
```

Both mechanisms flow through the same server-side boundary: the Policy, the route middleware, and the Livewire component action must all agree, and that agreement must live in one place (the Policy/Action), not be re-derived independently in each layer. Do not duplicate the same authorization logic across controllers, Livewire components, Blade views, and routes — each layer calls into the single authoritative check.

---

# 9. Canonical Admin Panel Access Rule

The single rule that decides whether a user may reach the Admin Application at all:

```text
A user has Admin Panel access if:

    the user is a Super Admin

    OR

    the user has the `access dashboard` permission
```

`access dashboard` — not `admin.dashboard` — is the canonical permission, per the approved naming convention (Section 7). It is checked directly, with no intermediate identifier: `$user->can('access dashboard')`, `@can('access dashboard')`, `middleware('can:access dashboard')` all reference the exact same Spatie permission. The Super Admin bypass (Section 11) makes the OR condition automatic without being written out at every call site.

Every other Admin-area authorization question (can this user view Users? create Roles?) is a *separate*, additional permission check layered on top of having reached the Admin Panel in the first place (Section 10).

UI visibility is not security. The Admin Panel link in the user dropdown (Section 16) is hidden from users who fail this rule, but hiding the link is a courtesy, not the enforcement mechanism. A user who manually navigates to `/admin/dashboard` without satisfying this rule must receive a `403` from the server, not merely fail to find a link to click.

---

# 10. Admin Route Authorization Architecture

```text
/admin/*
    ↓
authenticated          (auth middleware — already established in Phase 2)
    ↓
email verified          (verified middleware — already established in Phase 2)
    ↓
authorized              (new in Phase 3 — permission check)
```

These are three distinct concerns and must not be collapsed into one:

```text
auth middleware          → is there a logged-in session at all?
verified middleware       → has this session's user confirmed their email?
permission check          → is this specific user allowed to reach this specific route?
```

Not every Admin route requires the same permission. `access dashboard` gates the dashboard itself; a future Users index route requires `view users`; a future Roles index route requires `view roles`; and so on. The Admin route group's outer middleware only needs to guarantee "this user has *some* form of Admin Panel access" (Section 9); each individual route or route group within `routes/admin.php` layers its own specific permission requirement on top.

```php
// Conceptual shape for Phase 3 implementation — not yet applied
Route::middleware(['auth', 'verified', 'can:access dashboard'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');

        Route::middleware('can:view users')->group(function () {
            // future Users routes
        });

        Route::middleware('can:view roles')->group(function () {
            // future Roles routes
        });

        Route::middleware('can:view permissions')->group(function () {
            // future Permissions routes
        });
    });
```

Route middleware references the permission name directly (`can:access dashboard`, `can:view users`) — there is no separate Gate-ability layer to maintain (see Section 22 for why the previous revision's indirection was removed). Laravel's built-in `can:` middleware (or Spatie's `permission:` middleware alias) both route through the Gate system, which is why the Super Admin bypass in Section 11 must be registered at the Gate level rather than duplicated per-middleware.

**Implementation note for Phase 3:** verify empirically, with a test, that the installed Spatie Permission version (`^8.0`) allows a `Gate::before` callback registered in `AppServiceProvider` to short-circuit *both* `$user->can(...)` calls *and* the `permission`/`can` route middleware for a Super Admin. Package internals around Gate integration can vary by version; this must be proven with a passing test before the bypass is relied upon anywhere else, rather than assumed from documentation alone.

---

# 11. Super Admin Bypass Mechanism

The preferred mechanism is Laravel's native `Gate::before` hook, registered once in `AppServiceProvider::boot()`:

```php
use Illuminate\Support\Facades\Gate;

Gate::before(function (User $user, string $ability) {
    return $user->isSuperAdmin() ? true : null;
});
```

Returning `true` short-circuits the entire authorization check — every Policy method and every Spatie permission check for that user — to "allowed," for any ability, anywhere in the application. Returning `null` (not `false`) is essential: it means "I have no opinion," allowing the check to fall through to normal Policy/permission logic for everyone who is not a Super Admin.

**Why this over assigning every permission to Super Admin directly:** manually syncing every permission in the catalogue onto the Super Admin role is brittle — it requires updating a seeder every time a new permission is added anywhere in the application (including future 8bit projects built from this starter, and including every custom permission an Admin creates through the Permissions Manager, Section 13), and it makes "Super Admin" look, in the database, like an unusually large ordinary role rather than the categorically different concept it is. `Gate::before` keeps Super Admin's power implicit and automatic: a brand-new permission introduced by a future feature — or created through the UI — is available to Super Admin the moment it exists, with zero seeder maintenance.

**Implications to design around, not ignore:**

- `Gate::before` bypasses **ability checks**. It does not, and must not, bypass **hard business invariants** that are not expressed as an ability at all — most importantly, "never delete the last Super Admin" (Section 19). That rule must be enforced as an unconditional check inside the relevant Action, executed regardless of who is asking, including a Super Admin acting on themselves.
- Because the bypass is ability-name-agnostic, a bug in a future Policy that accidentally reuses an existing permission name could grant a Super Admin access to something unrelated. This is an argument for descriptive, unambiguous permission phrases (Section 7), not a reason to avoid `Gate::before`.
- Every route/action that relies on `$user->can(...)`, `@can`, `Gate::allows()`, or the `can:`/`permission:` middleware benefits from the bypass automatically. Any authorization check that does *not* go through one of those Laravel-native paths (for example, a raw `if ($user->hasPermissionTo(...))` called directly against Spatie without going through `Gate`) will **not** receive the bypass and must not be used for Super-Admin-sensitive checks. Standardize on `$user->can(...)` / Policies for this reason.

---

# 12. Roles Manager (Future)

The Admin Panel contains a first-class Roles Manager. Admin navigation includes a **Roles** item (Section 17). None of the UI described below exists yet.

## 12.1 Roles List

```text
Columns:
    ID
    Name
    Permissions   — displayed as compact badges/tags, not a raw count or a
                    dumped array; each badge shows one permission's
                    human-readable name (Section 7).
    Actions       — Edit, Delete

Pagination: standard, matching the existing table conventions established
elsewhere in the starter (CONVENTIONS.md §18 — 25 per page unless the UX
requires otherwise).
```

## 12.2 Role Create / Edit

```text
Fields:
    Name

    Permissions — a multi-column checkbox grid, one checkbox per existing
                  permission in the catalogue (queried, never hard-coded),
                  compact and responsive (collapsing to fewer columns on
                  narrower viewports, per UI-DESIGN-SYSTEM.md's general
                  responsive requirements). Selecting/unselecting a checkbox
                  toggles that permission's assignment to the role.
```

The grid renders whatever permissions currently exist — for a fresh install, the thirteen from the Initial Permission Catalogue (Section 7); for a project that has added its own via the Permissions Manager, those too, automatically, with no code change to the Roles Manager itself. This is what makes the design in Section 24 (Extensibility) concrete: the Roles Manager never needs to know about `view properties` or `create bookings` in advance.

## 12.3 Authorization Rules

```text
Creating a role
    Requires create roles.
    The name "Super Admin" (case-insensitive) is always rejected, from
    anyone, including a Super Admin. There is exactly one Super Admin role,
    and it is seeded, never created through the UI (see Section 15/18).

Editing a role
    Requires update roles.
    The Super Admin role itself cannot be edited (name, permissions, or
    otherwise) through the UI by anyone (Section 6.1, Rule 2).
    Attaching a protected/system permission (Section 13) to any role
    requires the actor to be a Super Admin, regardless of whether they hold
    update roles — checked individually, per checkbox, when the form is
    submitted (a normal Admin's form submission that includes a protected
    permission is rejected outright, not silently ignored).

Deleting a role
    Requires delete roles.
    The Super Admin role cannot be deleted.
    A role currently assigned to one or more users should require
    confirmation and an explicit decision about what happens to those users
    (reassign to a default role, or block deletion until reassigned) —
    Phase 3 implementation must choose one; blocking deletion until no users
    hold the role is the safer default.

Assigning permissions to a role
    Requires update roles.
    Protected/system permissions can only be attached by a Super Admin
    (Section 6.3).

Assigning a role to a user
    Requires update users (the actor must be allowed to modify the target
    user — see the UserPolicy example in Section 8) plus the ability to
    assign roles generally.
    Assigning the Super Admin role specifically requires the actor to
    already be a Super Admin (Section 6.2), independent of any other
    permission held.
```

A non-Super-Admin must never be able to create or manufacture Super Admin privileges through any combination of the operations above. Section 6 is the authoritative statement of why; this section describes where each rule attaches to the concrete Roles Manager UI.

---

# 13. Permissions Manager (Future)

The Admin Panel contains a first-class Permissions Manager. Admin navigation includes a **Permissions** item (Section 17). None of the UI described below exists yet.

## 13.1 Permission List

```text
Columns:
    ID
    Name
    Edit
    Delete

Also provides:
    Search       — filters by name, matching CONVENTIONS.md §17's search
                   property convention.
    Pagination   — standard table pagination.
    Create Permission — a primary action, per UI-DESIGN-SYSTEM.md §16.
```

## 13.2 Permission Create / Edit

```text
Fields:
    Name — the only field required initially. A human-readable capability
           name (Section 7), validated as required, string, and unique
           (case-insensitive, trimmed) against the existing permission
           catalogue.
```

Do not require technical dot notation such as `users.view`. The name the administrator types is the exact string that later appears in `@can('...')` and `middleware('can:...')` — there is no hidden internal identifier to keep in sync (Section 22).

## 13.3 Authorization Rules and the Protected-Permission Mechanism

**This reverses the previous revision of this document**, which recommended against exposing permission creation in the UI at all. The approved UX explicitly includes a Create Permission action. The exact protection mechanism, required by the brief before implementation proceeds, is as follows.

```text
Two categories of permission exist:

System permissions (protected) — exactly the thirteen from the Initial
    Permission Catalogue (Section 7): access dashboard, and the
    view/create/update/delete pairs for users, roles, and permissions.

Custom/application permissions — everything else, including anything an
    administrator creates through this Manager for their specific project.
```

**Maintain the protected set as a small, developer-owned, explicit list** — for example `config/permissions.php` returning the exact thirteen strings. A config file is version-controlled, code-reviewed, and cannot be altered at runtime by anyone with database/UI access, which is precisely the property needed here. As established in Section 6.3, this list **cannot** be a wildcard/prefix match, since permission names no longer have a hierarchical dot-notation shape to match against — every protected permission is named individually, in full.

```text
Viewing permissions
    Requires view permissions. No restriction on which rows are visible.

Creating a permission
    Requires create permissions.
    Rejected if the submitted name matches (case-insensitive, trimmed) any
    existing permission, protected or not — this is ordinary uniqueness
    validation, not a special rule, but it is what closes the "shadow a
    protected permission with a new row" attempt from Section 6.4.

Updating (renaming) a permission
    Requires update permissions.
    A protected permission cannot be renamed by anyone, including a Super
    Admin, through the UI (Section 6.4) — it is locked in the same way the
    Super Admin role is locked (Section 6.1, Rule 2).
    A custom permission cannot be renamed to match a protected permission's
    name (Section 6.4) — rejected for everyone, the same uniqueness check
    as creation.

Deleting a permission
    Requires delete permissions.
    A protected permission cannot be deleted by anyone, ever — deleting
    access dashboard or delete users would silently break the routes and
    Policies wired to check for it in code.
    Deleting a custom permission currently attached to any role should
    require confirmation or be blocked, at the implementation's discretion.

Assigning a permission to a role
    See Section 12; protected permissions require Super Admin regardless of
    who holds update roles or update permissions.
```

**Why this is safe despite allowing free permission creation:** a permission that is not referenced by any `$user->can(...)` / `@can(...)` / `middleware('can:...')` check anywhere in application code grants nothing — creating one is inert. The only way permission creation becomes dangerous is by colliding with, or overwriting, the name of a permission the codebase *does* check — which is exactly the protected set, and exactly what the uniqueness/immutability rules above prevent. An administrator is free to invent `view properties` for their own project; they are never free to invent, rename into, or delete `delete users`.

---

# 14. User Management vs User Profile

These are different features and must not be merged:

```text
User Profile
    Route: /profile
    An authenticated user managing their own account (name, email).
    Established in Phase 2. No permission required beyond being authenticated
    and verified — a user always has authority over their own profile.

User Management
    Route: /admin/users (future)
    An authorized administrator managing other users' accounts: listing,
    viewing, creating, editing, assigning roles, activating/deactivating,
    and deleting where appropriate.
    Requires view users / create users / update users / delete users as
    appropriate, plus the contextual UserPolicy checks from Section 8.
```

A user is never granted Admin Panel access merely because a User Management record exists for them, and creating a user through User Management never implicitly grants that new user any permissions — Admin access remains strictly permission-based (Section 3), assigned deliberately through the Roles Manager, never as a side effect of account creation.

---

# 15. First Super Admin Creation

There is no seeded Super Admin user. The role exists after seeding (Section 18); no account holds it until this command is run.

**Proposed command:**

```text
php artisan 8bit:create-super-admin
```

This matches the `8bit:` command namespace already anticipated in `FEATURE-SET.md` §70 (`8bit:install`, `8bit:setup`, `8bit:permissions`) and reads clearly as starter-specific tooling rather than a generic Laravel command.

**Behavior:**

```text
1. Console-only. No route, controller, or Livewire component ever wraps or
   exposes equivalent functionality. This command is the only way to create
   the first Super Admin.

2. Check whether a Super Admin already exists.
       If none exists: proceed normally.
       If one or more already exist: display the existing Super Admin(s)
       (name/email only — never any credential) and require explicit
       interactive confirmation (or a --force flag for non-interactive/CI
       use) before creating an additional one. Default behavior without
       confirmation is to abort, preventing an accidental extra Super Admin
       from a carelessly re-run deploy script.

3. Prompt for name, email, and password using Laravel Prompts
   (already available; no new package required):
       - name: required, string.
       - email: required, valid email format, unique against the users table.
       - password: entered via a hidden prompt, with confirmation, validated
         against Laravel's default password rules (Rules\Password::defaults()).

4. Create the user through the standard User model (respecting its existing
   $fillable and the `hashed` password cast established in Phase 1/2).

5. Mark the new user's email as verified immediately. This account is
   created by an operator with server/CLI access as part of deployment; the
   ordinary email-verification loop (Phase 2) exists to prove a self-registered
   user controls their inbox, which does not apply here, and requiring it
   would create a bootstrap deadlock (no other Admin exists yet to be trusted).

6. Ensure the Super Admin role exists (idempotent — defensively re-runs the
   same firstOrCreate the seeder uses, in case the command is run before
   `db:seed`), then assign it to the new user.

7. Never print, log, or persist the plaintext password anywhere. The hidden
   prompt already prevents terminal echo; the command must not additionally
   write it to any log channel, activity log entry, or output.

8. Report success with the created user's name and email only.
```

This command is safe to run once during initial deployment and safe to decline re-running (step 2) if accidentally invoked again later.

---

# 16. Admin Dropdown Access

The authenticated Member user dropdown (established in Phase 2, currently: Profile, Logout) gains one conditional entry:

```text
Profile
Admin Panel     ← shown only when the canonical rule in Section 9 is satisfied
Logout
```

```text
Super Admin                    → show "Admin Panel"
User with access dashboard     → show "Admin Panel"
Everyone else                  → do not show "Admin Panel"
```

Conceptually: `@can('access dashboard')` in the Blade dropdown — the exact same permission name the `/admin/dashboard` route itself requires (Section 9, Section 10), not a re-derived check and not a role-name check. Once the Section 11 bypass is registered, this evaluates `true` for Super Admins automatically without a separate `@if($user->isSuperAdmin())` branch. As stated in Section 9, this visibility check is a courtesy; the `/admin/*` routes enforce the same rule independently and are the actual security boundary.

---

# 17. Admin Navigation

The Admin sidebar (`resources/views/layouts/admin.blade.php`) gains **Roles** and **Permissions** items (Sections 12–13) and becomes permission-aware as each Admin feature is built:

```text
Dashboard         → access dashboard
Users             → view users
Roles             → view roles
Permissions       → view permissions
Activity Log      → view activity log
Settings          → view settings
```

Each `<x-menu-item>` is wrapped in the corresponding `@can(...)` check, using the exact same permission name its destination route requires (Section 10) — not a re-derived check. As with Section 16, this determines what a user *sees*, not what they may *do* — every route behind each nav item independently enforces the same permission server-side. Navigation must never be structured around role names (`@if($user->hasRole('Admin'))`); it is structured around the same permissions the routes themselves require, so the two can never drift out of sync.

---

# 18. Database / Seeding Strategy

Spatie's tables (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) are already migrated (Phase 1). Phase 3 adds no new migrations of its own for the roles/permissions system itself; it adds a seeder.

**Proposed seeder:** `RolePermissionSeeder`, matching the exact name already anticipated in `CONVENTIONS.md` §74/§75.

```text
RolePermissionSeeder responsibilities:
    - firstOrCreate each of the thirteen permissions in the Section 7
      Initial Permission Catalogue, using the exact human-readable strings.
    - firstOrCreate the three initial roles (Super Admin, Admin, Member).
    - Attach the full permission catalogue to Admin. The seeder runs as a
      trusted, developer-controlled process, not through the runtime
      authorization boundary described in Section 6 — the protections in
      Section 6.3/13.3 govern what an Admin can do afterward through the UI,
      not what the seeder itself is allowed to set up once, deliberately.
    - Attach no permissions to Member; Member's access is limited to what
      Phase 2 already established (/dashboard, /profile), which requires no
      Spatie permission at all.
    - Attach no permissions to Super Admin. Super Admin needs none, by design
      (Section 11) — this is itself a signal that the bypass is working as
      intended, since an empty permission set would otherwise mean "can do
      nothing."
    - Create no users. The seeder never creates a Super Admin account
      (Section 15) or any other authorization-bearing user.
```

`RolePermissionSeeder` must be idempotent: running `php artisan db:seed` multiple times creates no duplicate roles or permissions and produces no errors, using `firstOrCreate` throughout (matching `CONVENTIONS.md` §75). It is called from `DatabaseSeeder::run()` alongside the existing demo-user creation from Phase 1, but is conceptually independent essential system data, not demo data — consistent with the `SystemSeeder` / `DemoSeeder` separation described in `FEATURE-SET.md` §72, even though the starter currently keeps a single `DatabaseSeeder` file rather than splitting it (a decision Phase 3 implementation may revisit but is not required to).

`php artisan db:seed` is safe to run in any environment, including production, for the roles/permissions portion specifically, since it creates no accounts and only ever adds missing system rows.

---

# 19. Deletion & Protection Rules

```text
The Super Admin role
    Cannot be deleted or renamed by anyone, ever, through the UI (Section 6.1).

System/protected permissions (the thirteen from Section 7)
    Cannot be deleted or renamed by anyone through the UI (Section 6.4, 13.3).

A role currently assigned to one or more users
    Deletion is blocked until no users hold the role, unless Phase 3
    implementation deliberately builds a reassignment flow instead. Blocking
    is the safer default and requires no additional design.

A custom permission currently attached to one or more roles
    Deletion should require confirmation or be blocked, at the
    implementation's discretion (Section 13.3).

The currently authenticated user, in User Management
    A user cannot delete their own account through User Management
    (Section 8, UserPolicy::delete example). Account self-deletion, if ever
    offered, belongs to a distinct, deliberate Profile-area feature — not a
    side effect of User Management's generic delete action.

The last remaining Super Admin
    Cannot be deleted, deactivated, or have the Super Admin role removed,
    by anyone, including themselves. This is the one rule in this document
    that must be enforced as a hard invariant independent of the Section 11
    bypass — Gate::before grants ability, it does not know about this
    business rule, so the check belongs directly in the relevant Action
    (e.g. a DeleteUser or UpdateUserRoles action calls something like
    $target->isTheLastSuperAdmin() and refuses regardless of who is asking,
    including a Super Admin acting on another Super Admin or on themselves).
```

```php
// Conceptual invariant, enforced inside the Action layer, not the Gate
public function isTheLastSuperAdmin(): bool
{
    return $this->isSuperAdmin()
        && User::role(SystemRole::SuperAdmin->value)->count() === 1;
}
```

These rules exist to prevent two failure modes: a malicious or compromised Admin account permanently escalating itself, and an honest mistake permanently locking every administrator out of the Admin Panel. Where a rule required a judgment call not explicitly settled elsewhere in this document, the choice above is the more conservative/secure default, called out as such so it can be revisited deliberately rather than discovered by accident.

---

# 20. Activity Logging Considerations

The starter already includes Spatie Laravel Activitylog `^4.12` (Phase 1). Phase 3 does not implement any logging and does not modify the existing Activitylog configuration. It records, for a later implementation phase, which authorization-sensitive events are worth logging when the logging convention for this starter is designed:

```text
Role created / updated / deleted
Permission created / updated / deleted
Permission attached to / detached from a role
Role assigned to / removed from a user
A Super Admin account created
The Super Admin role assigned to a user
An attempt to perform a protected operation (Section 6) that was blocked —
    worth logging where practically useful, since a repeated blocked attempt
    to rename a role to "Super Admin", or to rename/delete a protected
    permission, is itself a meaningful security signal.
```

Consistent with `ARCHITECTURE.md` §27 ("Do not log every insignificant internal operation") and `CONVENTIONS.md` §67 (never log passwords, tokens, or secrets), any future implementation of this logging must record who performed the action and what changed, never any credential.

---

# 21. Livewire Architecture for Admin Authorization Features

Consistent with the structure already established in `ARCHITECTURE.md` (Phase 2.1) and unmodified here:

```text
app/Livewire/Admin/
├── Dashboard.php        (exists)
├── Users/
├── Roles/
└── Permissions/
```

```text
resources/views/livewire/admin/
```

All future Admin Livewire components use `#[Layout('layouts.admin')]`, matching `Admin/Dashboard.php` today. Coarse "may this user be here at all" authorization is the route's responsibility (Section 10), not repeated in every component's `mount()` — see Section 22 for the full reasoning and the resource-specific cases where a component-level or action-level check is still required regardless of the route boundary. A `@can` in a Blade view controls what renders; it is never a substitute for that resource-specific check, which controls what the component will actually do if invoked (for example, via a direct Livewire request that never re-renders the view at all).

Use the existing application stack — Livewire, Mary UI, Tailwind, daisyUI — for the Roles and Permissions Manager UI exactly as for every other Admin screen (Section 5). The approved UX screenshots are a reference for structure and behavior (table columns, the checkbox grid, search/pagination), not a pixel-exact specification; the starter's established design system (`UI-DESIGN-SYSTEM.md`) governs the actual visual treatment.

---

# 22. Admin Access Convention (Code-Level API)

No custom authorization helper is introduced, and — as of this revision — no separate Gate-ability layer either. The starter uses Laravel-native and Spatie-native APIs directly against the permission's own name, since both already provide everything needed:

```php
$user->can('access dashboard');      // Laravel's native Gate/Policy API,
                                      // Spatie-permission-aware out of the box.
                                      // Correct everywhere: Policies, components,
                                      // Blade, and route middleware alike.

$user->isSuperAdmin();               // The one centralized method from
                                      // Section 6.1, Rule 1 — not a global
                                      // helper function, a normal model method.

@can('access dashboard')             // Blade, for visibility only (Section 9,
                                      // Section 16).

Route::middleware('can:access dashboard') // Route-level enforcement
                                      // (Section 10) — the permission name
                                      // used directly as the middleware
                                      // argument, no intermediate identifier.
```

**Why the previous revision's `Gate::define`-backed indirection was removed:** the approved UX requires that the name an administrator types when creating a permission (Section 13.2) is the exact string later used in `@can(...)` and `middleware('can:...')`, with no hidden mapping step for a developer to maintain. Since Spatie automatically registers every permission as a Gate ability under its own name, `$user->can('access dashboard')` already resolves correctly with zero additional registration — introducing a parallel `Gate::define('access dashboard', ...)` would only duplicate what Spatie already provides, for no benefit, while adding a place the two names could drift apart. The indirection is not needed and is not part of the design.

This matches `ARCHITECTURE.md` §1 ("Use Laravel's conventions first. Add an abstraction only when it solves a real recurring problem") and `CLAUDE.md` §5 ("Laravel First")'s general preference for Laravel-native functionality over custom abstractions. `isSuperAdmin()` is the only addition on top of what Laravel and Spatie already provide, and it exists specifically because Section 6 requires exactly one place that performs that check.

**Livewire component responsibility, restated from the previous revision:** avoid repeating the route's coarse "may this user be here" check inside every full-page component's `mount()` (Section 21); a component-level or action-level check remains necessary whenever the action can be invoked independently of the page's route boundary (a `wire:click` handler is a separate request from the initial page load) or operates on a specific resource (a Policy question, Section 8, not a repeat of the area-level permission).

---

# 23. Security Principle — Never Trust the UI

Restated explicitly because it governs every section above: **no sensitive operation is ever authorized by the UI alone.** This applies uniformly to:

```text
Routes                    — middleware/can: checks (Section 10)
Livewire component actions — server-side authorize() calls (Section 21, 22)
Model mutations            — Policies consulted before save/delete (Section 8)
Role assignment             — Section 6.2
Permission creation/renaming — Section 6.4, 13.3
User management operations  — UserPolicy (Section 8)
Super Admin operations       — Section 6, enforced independent of any UI state
```

A hidden button, a disabled input, or a Livewire property that merely controls rendering are all legitimate and encouraged for user experience (`UI-DESIGN-SYSTEM.md` §97: "Hide actions they cannot perform when appropriate... Always enforce authorization server-side"). None of them are ever the only check. Every one of the operations above must independently re-verify authorization at the point the mutation actually happens, using the same centralized Policy/permission/Gate logic described in this document — never a re-derived or UI-layer-only version of the same rule.

---

# 24. Extensibility for Future Projects

The architecture above must work unmodified for a future 8bit Technologies project adding its own business permissions, created either through the Permissions Manager UI (Section 13) or a project-specific seeder:

```text
view properties
create properties
edit properties
```

or:

```text
view bookings
create bookings
cancel bookings
```

or:

```text
view events
create events
edit events
```

Adding these requires only: creating the permission (via the UI or a seeder entry), attaching it to whichever roles that project defines (Section 12), and a Policy/route check in the new feature's own code. Nothing in Sections 5–13 (the Super Admin/protection architecture) needs to change, because that architecture was deliberately built around a small, fixed *protected* set (Section 6.3, Section 13.3) plus an open-ended *custom* set — new business permissions always land in the custom set by default and never require touching the protection rules.

The starter itself does not define any business-specific permission beyond the thirteen-permission administrative catalogue in Section 7. Property, booking, event, or other domain permissions belong to the individual project that needs them, consistent with `FEATURE-SET.md`'s "build the foundation, not the finished application" principle.

---

# 25. Explicitly Out of Scope for This Document

This document describes design only. None of the following exist yet and none are created by this document:

```text
Roles CRUD
Permissions CRUD
User role assignment
Admin authorization
The 8bit:create-super-admin Artisan command
RolePermissionSeeder
Changes to the User model
Changes to routes/admin.php or routes/web.php
New middleware
New packages
New migrations
Any Livewire component
Any view
Any change to existing Phase 1/2/2.1 functionality
```

Phase 3 implementation begins only after this document is reviewed and approved.

---

# 26. Documentation Consistency Check

## 26.1 Contradiction found, reported, and since resolved

**`ARCHITECTURE.md` §17 (Permission Naming Convention) and `CONVENTIONS.md` §25 (Permissions)** previously established `resource.action` dot-notation as the permission naming convention for this starter (`customers.view`, `users.create`, `bookings.confirm`, etc.), and the previous revision of this document followed that convention directly (`admin.dashboard`, `users.view`).

**This revision's approved UX required human-readable, space-separated permission names instead** (`access dashboard`, `view users`) — stated directly in the brief this revision implements, including the explicit instruction that the Admin Dashboard permission "is therefore `access dashboard`, not `admin.dashboard`." At the time this revision was written, that was a genuine, direct contradiction with the then-current text of `ARCHITECTURE.md` §17 and `CONVENTIONS.md` §25, not a misreading.

Per the instruction accompanying this revision ("Only update: PHASE-3-ROLES-PERMISSIONS.md. Do not modify other documentation unless a direct contradiction is discovered."), the contradiction was reported here rather than silently resolved. **The project owner subsequently reviewed this report and explicitly instructed that `ARCHITECTURE.md` and `CONVENTIONS.md` be reconciled to match.** `ARCHITECTURE.md` §17 and `CONVENTIONS.md` §25/§28 have since been updated accordingly: both now document the `{verb} {resource}` convention exclusively, with all dot-notation examples removed and replaced by the same examples used in this document (`access dashboard`, `view users`, `create users`, etc.). All three documents — `ARCHITECTURE.md`, `CONVENTIONS.md`, and this document — are now in agreement. This section is retained as a historical record of the contradiction and its resolution, not as an indication of an open discrepancy.

## 26.2 Everything else checked

```text
ARCHITECTURE.md §16 (Roles and Permissions) — this document's Sections 4, 6
    extend it directly; nothing there mandates a specific naming grammar,
    so no contradiction beyond the one already noted in §17 above.
ARCHITECTURE.md §44 (Admin Route Structure, "Authentication vs
    Authorization") — already states the exact principle this document
    builds on ("Every Admin route and action must additionally enforce
    authorization... The eventual Admin authorization system combines
    Laravel authorization conventions + Spatie Laravel Permission").
    This document is the promised elaboration, not a contradiction.
CONVENTIONS.md §74/§75 (Seeders) — RolePermissionSeeder (Section 18) uses
    the exact naming and idempotency convention already specified.
CONVENTIONS.md §17/§18 (Search, Pagination) — the Permissions List's search
    field and both Managers' pagination (Sections 12.1, 13.1) follow the
    existing conventions directly.
UI-DESIGN-SYSTEM.md §16 (Primary Action) — "Create Permission" as a primary
    action (Section 13.1) matches the established convention.
FEATURE-SET.md §9 (Default Roles), §10 (Super Admin) — this document's
    Sections 4–6 are the detailed design FEATURE-SET.md defers to a later
    phase ("Do not scatter if ($user->is_admin) through application code").
PACKAGES.md §6 (Spatie Laravel Permission) — no version or configuration
    change proposed; this document uses the already-installed v8.3.0 as-is.
No change to CLAUDE.md, PACKAGES.md, PROJECT-BOOTSTRAP.md, or
    UI-DESIGN-SYSTEM.md was required or made.
```

---

# 27. Self-Review

```text
1.  Can a normal Admin manufacture Super Admin privileges?              No — Section 6.
2.  Can a normal Admin rename a role into Super Admin?                  No — Section 6.1, Rule 3.
3.  Can a normal Admin assign Super Admin?                              No — Section 6.2.
4.  Can a normal Admin manipulate permissions to escalate themselves?   No — Section 6.3, 6.4.
5.  Is Admin Panel access permission-based?                             Yes — Section 9 (access dashboard).
6.  Is Super Admin bypass clearly defined?                              Yes — Section 11 (Gate::before).
7.  Is first Super Admin creation clearly defined?                      Yes — Section 15.
8.  Is the last Super Admin protected?                                  Yes — Section 19.
9.  Are role names avoided for normal capability checks?                Yes — Section 4, permissions preferred.
10. Are permissions and Policies clearly distinguished?                 Yes — Section 8.
11. Is dynamic permission creation safe?                                Yes — Section 6.4, 13.3, collision/rename checks.
12. Is the architecture reusable for future projects?                   Yes — Section 24.
13. Does it fit the existing Spatie Permission installation?            Yes — v8.3.0, no changes proposed.
14. Does it fit the existing Activitylog v4 installation?               Yes — Section 20, advisory only.
15. Does it preserve PHP 8.3 compatibility?                             Yes — no new dependency introduced.
```
