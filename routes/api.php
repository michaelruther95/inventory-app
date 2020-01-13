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



// |--------------------------------------------------------------------------
// | PATIENT ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::get('get-patients', 'PatientsController@index');
		Route::post('create-new-patient', 'PatientsController@create');
		Route::post('update-patient', 'PatientsController@update');
		Route::delete('delete-patient', 'PatientsController@delete');
	});
	
// |--------------------------------------------------------------------------


// |--------------------------------------------------------------------------
// | APPOINTMENTS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::post('save-appointment', 'AppointmentsController@create');
	});
// |--------------------------------------------------------------------------


// |--------------------------------------------------------------------------
// | USERS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::get('get-users/{type}', 'UsersController@show');
	});
// |--------------------------------------------------------------------------





// Route::group(['middleware' => ['auth:api']], function (){ 
	
// });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
