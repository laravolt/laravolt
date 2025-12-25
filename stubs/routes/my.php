<?php

declare(strict_types=1);

use App\Http\Controllers\My\PasswordController;
use App\Http\Controllers\My\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    // My Profile
    Route::get('my/profile', [ProfileController::class, 'edit'])->name('my::profile.edit');
    Route::put('my/profile', [ProfileController::class, 'update'])->name('my::profile.update');

    // My Password
    Route::get('my/password', [PasswordController::class, 'edit'])->name('my::password.edit');
    Route::post('my/password', [PasswordController::class, 'update'])->name('my::password.update');
});
