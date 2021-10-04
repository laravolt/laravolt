<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // My Profile
    Route::get('my/profile', [\App\Http\Controllers\My\ProfileController::class, 'edit'])->name('my::profile.edit');
    Route::put('my/profile', [\App\Http\Controllers\My\ProfileController::class, 'update'])->name('my::profile.update');

    // My Password
    Route::get('my/password', [\App\Http\Controllers\My\PasswordController::class, 'edit'])->name('my::password.edit');
    Route::post('my/password', [\App\Http\Controllers\My\PasswordController::class, 'update'])->name('my::password.update');
});
