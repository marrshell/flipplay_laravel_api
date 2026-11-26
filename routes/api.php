<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::delete('/users/{user}', [AuthController::class, 'destroy']);

Route::apiResource('media', MediaController::class);

Route::get('/users/{user}/collection', [CollectionController::class, 'index']);
Route::post('/users/{user}/collection', [CollectionController::class, 'store']);
Route::delete('/users/{user}/collection/{media}', [CollectionController::class, 'destroy']);
