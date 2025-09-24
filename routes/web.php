<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::redirect('/', '/dashboard')->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    // attendance routes
    Route::prefix('attendance')->group(function () {
        Route::get('', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check_in');
        Route::get('check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check_out');
    });
    // task routes
    Route::prefix('task')->group(function () {
        Route::get('', [TasksController::class, 'index'])->name('task.index');
        Route::get('create', [TasksController::class, 'create'])->name('task.create');
        Route::post('upsert-task', [TasksController::class, 'upsert'])->name('task.upsert');
        Route::get('{tasks}', [TasksController::class, 'show'])->name('task.show');
        Route::delete('{tasks}', [TasksController::class, 'destroy'])->name('task.destroy');
    });
     // API routes
    Route::prefix('api')->group(function () {
        Route::get('employee/{user}/grade', [DashboardController::class, 'gradeApi']);
    });
});

require __DIR__ . '/auth.php';

Auth::routes();
