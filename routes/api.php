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

Route::get('articles','Articles@articles');
Route::get('articles/{id}/comments','Articles@comments');
Route::get('tags','Articles@tags');
Route::get('tags/{id}/articles','Articles@articlesByTag');
