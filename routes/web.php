<?php

use App\Http\Controllers\ObserverController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Observer関連のルート
    Route::get('/observer', [ObserverController::class, 'show'])->name('observer.show');
    Route::get('/observer/edit', [ObserverController::class, 'edit'])->name('observer.edit');
    Route::put('/observer', [ObserverController::class, 'update'])->name('observer.update');

    // Organization関連のルート
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organization.index');
    Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organization.create');
    Route::post('/organizations', [OrganizationController::class, 'store'])->name('organization.store');
    Route::get('/organizations/{organizationId}', [OrganizationController::class, 'show'])->name('organization.show');
    Route::get('/organizations/{organizationId}/edit', [OrganizationController::class, 'edit'])->name('organization.edit');
    Route::put('/organizations/{organizationId}', [OrganizationController::class, 'update'])->name('organization.update');
    
    // Subject関連のルート
    Route::get('/organizations/{organization}/subjects/create', [SubjectController::class, 'create'])->name('organization.subject.create');
    Route::post('/organizations/{organization}/subjects', [SubjectController::class, 'store'])->name('organization.subject.store');
    Route::get('/organizations/{organization}/subjects/{subject}', [SubjectController::class, 'show'])->name('organization.subject.show');
    Route::get('/organizations/{organization}/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('organization.subject.edit');
    Route::put('/organizations/{organization}/subjects/{subject}', [SubjectController::class, 'update'])->name('organization.subject.update');
});

require __DIR__.'/auth.php';
