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


// |--------------------------------------------------------------------------
// | AUTHENTICATION ROUTES
// |--------------------------------------------------------------------------
	Route::post('login', 'AuthController@create');
	Route::post('forgot-password', 'AuthController@create');
	Route::post('reset-password', 'AuthController@update');
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::get('fetch-auth-info', 'AuthController@index');
		Route::delete('logout', 'AuthController@delete');
	});
// |--------------------------------------------------------------------------



// Route::group(['middleware' => ['auth:api']], function (){ 
	
// });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
