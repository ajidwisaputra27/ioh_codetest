<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
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


//auth
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/refresh', [AuthController::class, 'refresh']);

Route::middleware(['auth:api'])->group(function () {
  //auth secured
  Route::post('auth/logout', [AuthController::class, 'logout']);
  Route::post('auth/me', [AuthController::class, 'me']);

  Route::resource('user', UserController::class);
  Route::resource('invoice', InvoiceController::class);
});
