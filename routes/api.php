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
// | PETS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::post('create-pet', 'PetsController@create');
		Route::post('update-pet', 'PetsController@update');
		Route::post('delete-pet', 'PetsController@delete');
	});
// |--------------------------------------------------------------------------



// |--------------------------------------------------------------------------
// | APPOINTMENTS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::post('save-appointment', 'AppointmentsController@create');
		Route::get('get-appointments', 'AppointmentsController@index');
		Route::post('reschedule-appointment', 'AppointmentsController@update');
		Route::post('cancel-appointment', 'AppointmentsController@delete');
	});
// |--------------------------------------------------------------------------



// |--------------------------------------------------------------------------
// | USERS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::get('get-users/{type}', 'UsersController@show');
	});
// |--------------------------------------------------------------------------



// |--------------------------------------------------------------------------
// | PRODUCTS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::get('get-products', 'ProductsController@show');
		Route::post('create-new-product', 'ProductsController@create');
		Route::post('update-product', 'ProductsController@update');
		Route::post('delete-product', 'ProductsController@delete');
	});
// |--------------------------------------------------------------------------



// |--------------------------------------------------------------------------
// | PRODUCT BATCHES ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){
		Route::post('create-product-batch', 'ProductBatchesController@create');
		Route::post('update-product-batch', 'ProductBatchesController@update');
	});
// |--------------------------------------------------------------------------



// |--------------------------------------------------------------------------
// | PRODUCT PURCHASE ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){
		Route::post('create-product-purchase', 'PurchasesController@create');
	});
// |--------------------------------------------------------------------------