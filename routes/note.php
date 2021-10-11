<?php

use App\Http\Controllers\MyNoteController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Note Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum')->prefix('/note')->group(function () {
    Route::post('/store', [MyNoteController::class, 'store'])
        ->name('note.store');

    Route::patch('/{note}/update', [MyNoteController::class, 'update'])
        ->name('note.update')
        ->middleware('can:update,note');
});