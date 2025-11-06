<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;


Route::get('/contacts', [ContactController::class, 'index']); // Listado
Route::get('/contacts/{id}', [ContactController::class, 'show']); // Ver un contacto
Route::post('/contacts', [ContactController::class, 'store']); // Crear contacto
Route::put('/contacts/{contact}', [ContactController::class, 'update']); // Actualizar contacto
Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']); // Eliminar contacto (softdelete)


Route::post('/contacts/{id}/process-score', [ContactController::class, 'processScore']);
