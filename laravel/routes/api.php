<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\DBEditorController;

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

// admin api
Route::group(["prefix" => "admin", "middleware" => "admin"], function () {
    Route::delete("db/delete", [DBEditorController::class, "api_delete"]);
    Route::put("/db/update", [DBEditorController::class, "api_update"]);
    Route::post("/db/add", [DBEditorController::class, "api_add"]);
});
