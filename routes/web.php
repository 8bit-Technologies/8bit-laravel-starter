<?php

use App\Http\Middleware\EnsureEmailIsVerifiedIfRequired;
use App\Livewire\Dashboard;
use App\Livewire\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', EnsureEmailIsVerifiedIfRequired::class])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('profile', Profile::class)->name('profile');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
