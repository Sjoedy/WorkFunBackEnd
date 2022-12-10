<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    /*
     * Group management
     */
    Route::apiResource('group', GroupController::class)->except(['destroy']);
    Route::post('join/group', [GroupController::class, 'joinGroup']);
    Route::get('list/group/user/{groupId}', [GroupController::class, 'listGroupUser']);

    Route::get('logout', [AuthController::class, 'logout']);
});
