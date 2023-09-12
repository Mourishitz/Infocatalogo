<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Users

Route::apiResource('/user', UserController::class)->except(['destroy']);
Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');

// Posts

Route::apiResource('/post', PostController::class)
    ->middleware('auth:sanctum')
    ->except(['index', 'show']);
Route::get('/post', [PostController::class, 'index']);
Route::get('/post/{id}', [PostController::class, 'show']);

// Comments

Route::apiResource('/comment', CommentController::class)
    ->middleware('auth:sanctum')
    ->except(['index', 'show']);
Route::get('/comment', [CommentController::class, 'index']);
Route::get('/comment/{id}', [CommentController::class, 'show']);
