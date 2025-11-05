<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/ping', fn() => response()->json(['message' => 'pong']));

Route::apiResource('contacts', ContactController::class);

Route::post('/contacts/{id}/process-score', [ContactController::class, 'processScore']);
