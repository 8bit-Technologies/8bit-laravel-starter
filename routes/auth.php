<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', Register::class)->name('register');
    Route::get('login', Login::class)->name('login');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmail::class)->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->intended(route('dashboard', absolute: false));
    })->middleware('signed')->name('verification.verify');

    Route::post('email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('logout', function (Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});
