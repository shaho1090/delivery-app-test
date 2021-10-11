<?php

use App\Http\Controllers\MyNoteController;
use App\Models\Note;
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

    Route::get('/index', [MyNoteController::class, 'index'])
        ->name('note.index');

    Route::get('/show/{note}', [MyNoteController::class, 'show'])
        ->name('note.show')
        ->middleware('can:view,note');

    Route::delete('/delete/{note}', [MyNoteController::class, 'destroy'])
        ->name('note.delete')
        ->middleware('can:delete,note');
});