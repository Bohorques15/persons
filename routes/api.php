<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Auth routes
Route::group(['prefix' => 'auth'], function() {
	Route::post('login', 'AuthController@login');
	Route::group(['middleware' => 'auth:api'], function() {
		Route::post('logout', 'AuthController@logout');
	});
});

//Persons routes
Route::group(['middleware' => 'auth:api'], function() {
	Route::apiResource('persons', 'PersonsController', [
		'as' => 'api.persons',
	])
	->parameters(['persons' => 'person'])
	->only('index', 'store','update','destroy','show');
});
