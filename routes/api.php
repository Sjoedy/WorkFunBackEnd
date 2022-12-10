<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
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
    //check user has group
    Route::get('user/has/group', [GroupController::class, 'userHasGroup']);

    /*
     * challenge management
     */
    Route::apiResource('challenge', ChallengeController::class);

    Route::get('logout', [AuthController::class, 'logout']);
});
