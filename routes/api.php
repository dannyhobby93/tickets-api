<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// localhost:8000/api/v1/tickets/1/delete
// domain/api/version/resource/{identifier}/action/
// Resources - Models
// tickets
// users
// support contracts

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('/tickets', function () {
    return Ticket::all();
});