<?php

use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Home;

Route::get('/', Home::class)->name('home');
Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('auth');
