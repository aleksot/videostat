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

Route::middleware('client')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['client', 'check_api_stream'])->get('/stream', 'Api\StreamController@index');
Route::middleware(['client', 'check_api_stream'])->get('/stream/viewers', 'Api\StreamController@viewers');
