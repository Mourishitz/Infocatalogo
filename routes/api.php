<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LikeController;
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
    ->except(['index', 'show', 'store']);
Route::get('/comment', [CommentController::class, 'index']);
Route::get('/comment/{id}', [CommentController::class, 'show']);
Route::post('/comment/post/{id}', [CommentController::class, 'store'])->middleware('auth:sanctum');
Route::get('/comment/post/{id}', [CommentController::class, 'postComments']);

// Likes
Route::post('/like/post/{id}', [LikeController::class, 'likePost'])->middleware('auth:sanctum');
Route::get('/like/post/{id}', [LikeController::class, 'getPostLikes']);
Route::delete('/like/post/{id}', [LikeController::class, 'dislikePost'])->middleware('auth:sanctum');

Route::post('/like/comment/{id}', [LikeController::class, 'likeComment'])->middleware('auth:sanctum');
Route::get('/like/comment/{id}', [LikeController::class, 'getCommentLikes']);
Route::delete('/like/comment/{id}', [LikeController::class, 'dislikeComment'])->middleware('auth:sanctum');

// Feed
Route::get('/feed/top-posts/likes', [FeedController::class, 'mostLikedPosts']);
Route::get('/feed/top-posts/comments', [FeedController::class, 'mostCommentedPosts']);
Route::get('/feed/top-voices/likes', [FeedController::class, 'mostLikedAuthors']);
Route::get('/feed/top-voices/comments', [FeedController::class, 'mostCommentedAuthors']);
