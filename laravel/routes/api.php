<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



/**
 * Public api
 */
Route::prefix('/')->group(function () {
});

/**
 * Resident's api
 */
Route::group(['prefix' => 'resident', 'middleware' => 'resident'], function () {
	
});

/**
 * Admin's api
 */
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {	
	Route::delete('db/delete', 'admin\DBEditorController@api_delete');
	
	Route::put('/db/update', 'admin\DBEditorController@api_update');
	
	Route::post('/db/add', 'admin\DBEditorController@api_add');
});
