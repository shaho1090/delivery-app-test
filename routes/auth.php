<?php


use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterUserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/

Route::post('/register', [RegisterUserController::class, 'store'])
    ->middleware('guest')
    ->name('register-user');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('logout');