<?php

use App\Http\Controllers\ControllerChildrens;
use App\Http\Controllers\ControllerInstitution;
use App\Http\Controllers\ControllerUsers;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('users')->group(function () {
    // Route::post('register', [ControllerUsers::class, 'register']);

    Route::post('login', [ControllerUsers::class, 'login']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('register', [ControllerUsers::class, 'register']);
        Route::post('logout', [ControllerUsers::class, 'logout']);
        Route::post('update/{id}', [ControllerUsers::class, 'update']);
        Route::get('index', [ControllerUsers::class, 'index']);
        Route::post('destroy/{id}', [ControllerUsers::class, 'destroy']);
    });
    Route::prefix('childrens')->group(function () {
        Route::post('created', [ControllerChildrens::class, 'created']);
        Route::post('update/{id}', [ControllerChildrens::class, 'update']);
        Route::get('index', [ControllerChildrens::class, 'index']);
        Route::get('rfc', [ControllerChildrens::class, 'rfc']);
        Route::get('charts', [ControllerChildrens::class, 'charts']);

        
        Route::post('destroy/{id}', [ControllerChildrens::class, 'destroy']);
    });
    Route::prefix('institution')->group(function () {
        Route::post('created', [ControllerInstitution::class, 'created']);
        Route::post('update/{id}', [ControllerInstitution::class, 'update']);
        Route::get('show', [ControllerInstitution::class, 'show']);
        Route::get('index', [ControllerInstitution::class, 'index']);
        Route::post('destroy/{id}', [ControllerInstitution::class, 'destroy']);
    });
});
