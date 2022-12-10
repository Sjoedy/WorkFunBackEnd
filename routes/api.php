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
    //apply to join group
    Route::post('join/group', [GroupController::class, 'joinGroup']);
    //show member in group
    Route::get('group/info/{groupId}', [GroupController::class, 'groupInfo']);

    Route::get('logout', [AuthController::class, 'logout']);
});
