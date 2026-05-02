<?php

use App\Http\Controllers\Api\ProjectApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/projects',                    [ProjectApiController::class, 'store']);
    Route::patch('/projects/{project}/approve', [ProjectApiController::class, 'approve']);
});
