<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;
//register
Route::post('register', [AuthController::class, 'register']);
//login
Route::post('login', [AuthController::class, 'login']);
Route::group(['middleware' => ['auth:api']], function () {
    //self info
    Route::get('me', [AuthController::class, 'me']);
    //other info
    Route::get('other/{id}', [AuthController::class, 'other']);
    //group management
    Route::apiResource('group', GroupController::class)->except(['destroy']);
    //user apply to join group
    Route::post('join/group', [GroupController::class, 'joinGroup']);
    //show member in group
    Route::get('info/group', [GroupController::class, 'groupInfo']);
    //check user has group
    Route::get('user/has/group', [GroupController::class, 'userHasGroup']);
    //challenge management
    Route::apiResource('challenge', ChallengeController::class)->except(['destroy']);
    //user update challenge
    Route::put('update/challenge/{id}', [ChallengeController::class, 'updateChallenge']);
    //logout
    Route::get('logout', [AuthController::class, 'logout']);
});
