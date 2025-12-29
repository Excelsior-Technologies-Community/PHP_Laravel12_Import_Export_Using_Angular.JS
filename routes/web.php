<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudController;

// =============================
// PAGES
// =============================
Route::get('/crud', [CrudController::class, 'indexPage']);
Route::get('/crud/create', [CrudController::class, 'createPage']);
Route::get('/crud/edit', [CrudController::class, 'editPage']);

// =============================
// CRUD API
// =============================
Route::get('/crud-list', [CrudController::class, 'list']);
Route::post('/crud-store', [CrudController::class, 'store']);
Route::get('/crud-show/{id}', [CrudController::class, 'show']);
Route::post('/crud-update/{id}', [CrudController::class, 'update']);
Route::get('/crud-delete/{id}', [CrudController::class, 'destroy']);

// =============================
// IMPORT / EXPORT
// =============================
Route::get('/crud-export', [CrudController::class, 'export']);
Route::post('/crud-import', [CrudController::class, 'import']);
