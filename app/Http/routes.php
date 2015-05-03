<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::get('users', 'UserController@index');

Route::get('user/{id}', 'UserController@showProfile');

Route::get('search_users', 'UserController@findProfile');

Route::get('interface', 'InterfaceController@showInterface');

Route::get('speak', 'InterfaceController@speak');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
