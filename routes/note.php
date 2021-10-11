<?php

use App\Http\Controllers\MyNoteController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Note Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum')->prefix('/note')->group(function (){
    Route::post('/store', [MyNoteController::class, 'store'])
        ->name('note.store');
});