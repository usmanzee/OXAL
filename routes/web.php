<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'admin'], function() {

	Route::group(['prefix' => 'users'], function() {
		Route::get('/', 'UsersController@index');
		Route::get('add', 'UsersController@add');
		Route::post('store', 'UsersController@store');
		Route::get('edit/{id}', 'UsersController@edit');
		Route::post('update/{id}', 'UsersController@update');
	});

	Route::group(['prefix' => 'categories'], function() {
		Route::get('/', 'CategoriesController@index');
	});

	Route::group(['prefix' => 'products'], function() {
		Route::get('/', 'ProductsController@index');
	});
});
