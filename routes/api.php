<?php

use App\Http\Controllers\AuthController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// localhost:8000/api/v1
// domain/api/resource/{identifier}/action/
// Resources - Models
// tickets
// users
// contracts

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/tickets', function () {
    return Ticket::all();
});