<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Protected System Permissions
    |--------------------------------------------------------------------------
    |
    | These are the permissions the Admin Panel and its own management
    | screens depend on directly in code. They must never be deletable,
    | renamable, or duplicable through the application UI, by anyone,
    | including a Super Admin — see PHASE-3-ROLES-PERMISSIONS.md §6.4/13.3.
    |
    | This list is deliberately a plain, developer-owned array rather than
    | a database flag: it is version-controlled, code-reviewed, and cannot
    | be altered at runtime by anyone with database/UI access.
    |
    */

    'protected' => [
        'access dashboard',

        'view users',
        'create users',
        'update users',
        'delete users',

        'view roles',
        'create roles',
        'update roles',
        'delete roles',

        'view permissions',
        'create permissions',
        'update permissions',
        'delete permissions',
    ],

];
