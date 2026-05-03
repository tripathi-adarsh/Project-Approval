<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/',          [ProjectController::class, 'index'])->name('index');
        Route::get('/create',    [ProjectController::class, 'create'])->name('create');
        Route::post('/',         [ProjectController::class, 'store'])->name('store');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/projects',                     [ProjectController::class, 'adminIndex'])->name('projects.index');
        Route::post('/projects/bulk',               [ProjectController::class, 'bulk'])->name('projects.bulk');
        Route::patch('/projects/{project}/approve', [ProjectController::class, 'approve'])->name('projects.approve');
        Route::patch('/projects/{project}/reject',  [ProjectController::class, 'reject'])->name('projects.reject');
        Route::get('/audit-logs',                   [AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('/users',                        [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',                 [UserController::class, 'create'])->name('users.create');
        Route::post('/users',                       [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}',              [UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__ . '/auth.php';
