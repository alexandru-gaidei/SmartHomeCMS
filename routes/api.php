<?php

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

Route::namespace('Api')->prefix('v1')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('users/my', 'UserController@my');
        Route::post('users/logout', 'UserController@logout');
        Route::apiResource('users', 'UserController');
        Route::apiResource('groups', 'GroupController');

        Route::get('sensors/metadata', 'SensorController@metadata');
        Route::patch('sensors/{sensor}/favorite', 'SensorController@favorite');
        Route::apiResource('sensors', 'SensorController');

        Route::get('actions/metadata', 'ActionController@metadata');
        Route::patch('actions/{action}/favorite', 'ActionController@favorite');
        Route::apiResource('actions', 'ActionController');

        Route::apiResource('history', 'HistoryController')->only(['index']);

        Route::post('favorite/reorder', 'FavoriteController@reorder');
        Route::post('favorite/do/{favorite}/action', 'FavoriteController@doAction');
        Route::apiResource('favorite', 'FavoriteController')->only(['index']);

        Route::get('cams/metadata', 'VideoCameraController@metadata');
        Route::get('cams/{cam}/recording/start', 'VideoCameraController@startRecording');
        Route::get('cams/{cam}/recording/stop', 'VideoCameraController@stopRecording');
        Route::get('cams/{cam}/records', 'VideoCameraController@records');
        Route::apiResource('cams', 'VideoCameraController');

        Route::get('health/check', 'HealthCheckController@index');
    });

    Route::any('push/{identifier}', 'IncomingActionController@process')->name('push');
});
