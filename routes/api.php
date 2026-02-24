<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\AIController;
use Illuminate\Support\Facades\Route;

Route::post('auth/token', [AuthController::class, 'token']);

Route::middleware(['auth:sanctum', 'tenant', 'openai.cost'])->group(function () {
    Route::apiResource('contacts', ContactController::class);
    Route::get('contacts/{contact}/interactions', [InteractionController::class, 'index']);
    Route::apiResource('interactions', InteractionController::class)->except(['index']);
    Route::apiResource('tags', TagController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('reminders', [ReminderController::class, 'index']);
    Route::post('reminders/{contact}', [ReminderController::class, 'store']);
    Route::post('ai/stay-in-touch', [AIController::class, 'stayInTouch']);
    Route::post('ai/suggest-reminders', [AIController::class, 'suggestReminders']);
});
