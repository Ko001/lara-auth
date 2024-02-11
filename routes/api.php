<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('/', function () {
//     return response()->json(['message' => 'Hello, World!'], 200);
// });

Route::post('/signup', [UserController::class, 'signup']);
Route::get('/users/{user_id}', [UserController::class, 'getUser']);
Route::patch('/users/{user_id}', [UserController::class, 'updateUser']);
Route::post('/close', [UserController::class, 'deleteUser']);
