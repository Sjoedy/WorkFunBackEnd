<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminChallengeController;
use App\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    //self info
    Route::get('me',[AuthController::class, 'me']);
    /*
     * Group management
     */
    Route::apiResource('group', GroupController::class)->except(['destroy']);
    //apply to join group
    Route::post('join/group', [GroupController::class, 'joinGroup']);
    //show member in group
    Route::get('info/group', [GroupController::class, 'groupInfo']);
    //check user has group
    Route::get('user/has/group', [GroupController::class, 'userHasGroup']);

    /*
     * admin challenge management
     */
    Route::apiResource('challenge', AdminChallengeController::class)->except(['destroy']);

    Route::get('logout', [AuthController::class, 'logout']);
});
