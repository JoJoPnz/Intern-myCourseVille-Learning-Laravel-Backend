<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookTypeController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', function () {
    echo "LOGIN PAGE";
})->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::post('/bookTypes', [BookTypeController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    Route::get('/stores', [StoreController::class, 'index']);
    Route::post('/stores', [StoreController::class, 'store']);
    Route::patch('/stores/{id}', [StoreController::class, 'update']);
    Route::delete('/stores/{id}', [StoreController::class, 'destroy']);

    Route::post('/books', [BookController::class, 'store']);
});

// Route::apiResource('stores', StoreController::class);


// Route::get('/login', function () {
//     return response()->json('go to login', 200);
// })->name('login');
