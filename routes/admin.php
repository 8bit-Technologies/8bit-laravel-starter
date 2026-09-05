<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Permissions;
use App\Livewire\Admin\Roles;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', Dashboard::class)
            ->name('dashboard')
            ->middleware('can:access dashboard');

        Route::middleware('can:view roles')->group(function () {
            Route::get('roles', Roles\Index::class)->name('roles.index');
        });

        Route::middleware('can:create roles')->group(function () {
            Route::get('roles/create', Roles\Create::class)->name('roles.create');
        });

        Route::middleware('can:update roles')->group(function () {
            Route::get('roles/{role}/edit', Roles\Edit::class)->name('roles.edit');
        });

        Route::middleware('can:view permissions')->group(function () {
            Route::get('permissions', Permissions\Index::class)->name('permissions.index');
        });

        Route::middleware('can:create permissions')->group(function () {
            Route::get('permissions/create', Permissions\Create::class)->name('permissions.create');
        });

        Route::middleware('can:update permissions')->group(function () {
            Route::get('permissions/{permission}/edit', Permissions\Edit::class)->name('permissions.edit');
        });
    });
