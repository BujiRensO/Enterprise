<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SampleDataController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Transactions
    Route::resource('transactions', TransactionController::class);
    
    // Budgets
    Route::resource('budgets', BudgetController::class);
    
    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
    Route::post('/reports/generate', [ReportsController::class, 'generate'])->name('reports.generate');

    // Goals
    Route::resource('goals', GoalController::class);
    Route::patch('/goals/{goal}/progress', [GoalController::class, 'updateProgress'])->name('goals.update-progress');

    // Payments
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::post('/payments/{payment}/details', [PaymentController::class, 'enterPaymentDetails'])->name('payments.details');
    Route::post('/payments/{payment}/verify-otp', [PaymentController::class, 'verifyOTP'])->name('payments.verify-otp');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // Sample Data
    Route::get('/seed-data', [SampleDataController::class, 'seed'])->name('seed.data');
});

require __DIR__.'/auth.php';