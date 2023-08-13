<?php

use App\Http\Controllers\MoviesController;
use App\Http\Controllers\TokenController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

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
Route::post('/tokens/create', [TokenController::class, 'createToken']);

Route::middleware('auth:sanctum')->get('/films/store', [MoviesController::class, 'store']);
Route::middleware('auth:sanctum')->get('/films/list', [MoviesController::class, 'index']);
Route::middleware('auth:sanctum')->get('/films/list/{id}', [MoviesController::class, 'show']);

