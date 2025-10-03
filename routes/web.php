<?php

use App\Http\Controllers\EmergencyProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEmergencyCardController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    $user?->loadMissing('emergencyProfile', 'emergencyCard');

    return Inertia::render('Dashboard', [
        'emergencyProfile' => $user?->emergencyProfile,
        'emergencyCard' => $user?->emergencyCard,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/emergency/profile', [EmergencyProfileController::class, 'show'])->name('emergency.profile.show');
    Route::patch('/emergency/profile', [EmergencyProfileController::class, 'update'])->name('emergency.profile.update');
    Route::post('/emergency/profile/pin', [EmergencyProfileController::class, 'regeneratePin'])->name('emergency.profile.pin');
    Route::post('/emergency/profile/qr', [EmergencyProfileController::class, 'regenerateQr'])->name('emergency.profile.qr');
});

Route::get('/card/{token}', [PublicEmergencyCardController::class, 'show'])->name('card.public.show');
Route::post('/card/{token}/unlock', [PublicEmergencyCardController::class, 'unlock'])->name('card.public.unlock');

require __DIR__.'/auth.php';
