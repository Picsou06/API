<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\hasShareInformation;
use App\Http\Controllers\requestSessionController;
use App\Http\Controllers\SessionListController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard')->middleware(hasShareInformation::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware(hasShareInformation::class);
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/request-session', [requestSessionController::class, 'index'])->name('request-session.index');
    Route::post('/request-session', [requestSessionController::class, 'store'])->name('request-session.store');
    Route::get('/session/{session}', [SessionController::class, 'show'])->name('session.show');
    Route::get('/session', [SessionListController::class, 'index'])->name('session.index');
});

require __DIR__.'/auth.php';
