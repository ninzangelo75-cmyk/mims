<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->middleware('guest');

Route::get('/login', [LoginController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LogoutController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Medicine Master (Admin only)
    Route::resource('items', \App\Http\Controllers\ItemController::class);

    // Receiving (Staff/Admin)
    Route::resource('receiving', \App\Http\Controllers\ReceivingController::class)->only(['index', 'create', 'store']);

    // Inventory (All authenticated users)
    Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');

    // Requests
    Route::prefix('requests')->name('requests.')->group(function () {
        Route::resource('ris', \App\Http\Controllers\RequestRisController::class)->only(['index', 'create', 'store']);
        Route::resource('ptr', \App\Http\Controllers\RequestPtrController::class)->only(['index', 'create', 'store']);
    });

    // Approvals & Releasing
    Route::get('/approvals', [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/ris/{requestRis}/approve', [\App\Http\Controllers\ApprovalController::class, 'approveRis'])->name('approvals.ris.approve');
    Route::post('/approvals/ptr/{requestPtr}/approve', [\App\Http\Controllers\ApprovalController::class, 'approvePtr'])->name('approvals.ptr.approve');
    
    Route::get('/releasing/ris/{requestRis}', [\App\Http\Controllers\ReleasingController::class, 'showRis'])->name('releasing.ris.show');
    Route::post('/releasing/ris/{requestRis}', [\App\Http\Controllers\ReleasingController::class, 'releaseRis'])->name('releasing.ris.store');

    // Audit Logs (Admin only)
    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');

    // Reports (placeholder page)
    Route::get('/reports', function () {
        return Inertia::render('Reports/Index');
    })->name('reports.index');

    // User Account (Admin manages users)
    Route::get('/account', [\App\Http\Controllers\UserAccountController::class, 'index'])->name('account.index');
    Route::post('/account', [\App\Http\Controllers\UserAccountController::class, 'store'])->name('account.store');
});
