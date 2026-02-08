<?php

use App\Http\Controllers\PtrController;
use App\Http\Controllers\RisController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ris', [RisController::class, 'store']);
    Route::post('/ris/{ris}/submit', [RisController::class, 'submit']);
    Route::post('/ris/{ris}/approve', [RisController::class, 'approve']);
    Route::post('/ris/{ris}/reject', [RisController::class, 'reject']);
    Route::post('/ris/{ris}/issue', [RisController::class, 'issue']);
    Route::get('/ris/{ris}', [RisController::class, 'show']);

    Route::post('/ptr', [PtrController::class, 'store']);
    Route::post('/ptr/{ptr}/submit', [PtrController::class, 'submit']);
    Route::post('/ptr/{ptr}/verify', [PtrController::class, 'verify']);
    Route::post('/ptr/{ptr}/approve', [PtrController::class, 'approve']);
    Route::post('/ptr/{ptr}/reject', [PtrController::class, 'reject']);
    Route::get('/ptr/{ptr}', [PtrController::class, 'show']);
});
