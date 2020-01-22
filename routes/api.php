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

Route::post('authenticate', 'AuthenticateController@authenticate');
Route::group(['middleware' => 'jwt.auth'], function () {
  Route::get('me', 'AuthenticateController@getCurrentUser');
});

Route::post('register', 'RegisterController@register');
Route::post('activate', 'RegisterController@activate');

// CRUDのURI自動設定される
Route::apiResource('images', 'ImagesController');
Route::apiResource('albums', 'AlbumsController');
Route::apiResource('voices', 'VoicesController');

Route::post('familyregister', 'FamilyregisterController@register');
Route::post('familyactivate', 'FamilyregisterController@activate');

Route::get('family', 'FamiliesController@index');
// Route::get('me', 'AuthenticateController@getCurrentUser');
