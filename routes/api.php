<?php

use App\Http\Controllers\TestController;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/test', [TestController::class, 'test']);

Route::middleware('auth:sanctum')->get('/client/me', function (Request $request) {
    return response()->json(new ClientResource($request->user()));
});

