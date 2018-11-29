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

Route::group(['prefix' => 'v1'], function() {
	Route::post('register', 'UsersController@register');
	Route::post('login', 'UsersController@login');
	Route::post('get-all-categories', 'CategoriesController@getAllCategories');
	Route::post('get-user-detail', 'UsersController@getUserDetail');
	Route::post('update-user-phone-number-and-send-verification-code', 'UsersController@updateUserPhoneNumberAndSendVerificationCode');
	Route::post('post-ad', 'ProductsController@postAd');
	Route::post('get-user-products', 'ProductsController@getUserProducts');
	Route::post('get-featured-products', 'ProductsController@getFeaturedProducts');
	Route::post('get-products-by-category', 'ProductsController@getProductsByCategory');
	Route::post('get-product-detail', 'ProductsController@getProductDetail');
});

