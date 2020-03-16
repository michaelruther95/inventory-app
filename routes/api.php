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
		Route::post('update-account', 'AuthController@update');
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
// | APPOINTMENTS ROUTES & PET APPOINTMENTS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::post('save-appointment', 'AppointmentsController@create');
		Route::get('get-appointments', 'AppointmentsController@index');
		Route::post('reschedule-appointment', 'AppointmentsController@update');
		Route::post('cancel-appointment', 'AppointmentsController@delete');
		Route::post('finish-appointment', 'AppointmentsController@finish');

		Route::post('submit-doctor-findings', 'PetAppointmentsController@update');

	});
// |--------------------------------------------------------------------------



// |--------------------------------------------------------------------------
// | USERS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function (){ 
		Route::get('get-users/{type}', 'UsersController@show');
		// Route::post('update-account', 'UsersController@update');

		Route::get('get-all-users', 'UsersManagementController@show');
		Route::post('create-new-user', 'UsersManagementController@create');
		Route::post('change-user-status/{status}', 'UsersManagementController@update');
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
		Route::post('purchase-multiple-product', 'MultiplePurchaseController@create');
	});
// |--------------------------------------------------------------------------



// |--------------------------------------------------------------------------
// | APPOINTMENT REMINDERS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function(){
		Route::post('send-reminder', 'AppointmentRemindersController@create');
	});
// |--------------------------------------------------------------------------



// |--------------------------------------------------------------------------
// | INVOICES ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function(){
		Route::get('get-invoices', 'InvoicesController@index');
		Route::post('void-invoice', 'InvoicesController@delete');
	});
// |--------------------------------------------------------------------------


// |--------------------------------------------------------------------------
// | INVOICES ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function(){
		Route::get('get-dashboard-resources', 'DashboardController@index');
	});
// |--------------------------------------------------------------------------


// |--------------------------------------------------------------------------
// | SUPPLIER ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function(){
		Route::get('get-suppliers', 'SuppliersController@index');
		Route::post('create-new-supplier', 'SuppliersController@create');
		Route::post('update-supplier', 'SuppliersController@update');
	});
// |--------------------------------------------------------------------------


// |--------------------------------------------------------------------------
// | MEDICAL RECORDS ROUTES
// |--------------------------------------------------------------------------
	Route::group(['middleware' => ['auth:api']], function(){
		Route::get('get-medical-records', 'MedicalRecordsController@index');
	});
// |--------------------------------------------------------------------------
