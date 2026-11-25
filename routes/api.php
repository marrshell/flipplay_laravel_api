<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media', MediaController::class);

Route::get('/users/{user}/collection', [CollectionController::class, 'index']);
Route::post('/users/{user}/collection', [CollectionController::class, 'store']);
Route::delete('/users/{user}/collection/{media}', [CollectionController::class, 'destroy']);
