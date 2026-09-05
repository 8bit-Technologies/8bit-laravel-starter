<?php

use App\Livewire\Admin\Dashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', Dashboard::class)
            ->name('dashboard')
            ->middleware('can:access dashboard');
    });
