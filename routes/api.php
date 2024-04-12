<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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
//User Route
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/filter_book',[BookController::class, 'filter']);

//Admin Route
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::resource('/book', BookController::class);
    Route::get('/view_borrowBook',[BookController::class, 'borrow']);
    Route::get('/view_returnBook',[BookController::class, 'return']);
});
