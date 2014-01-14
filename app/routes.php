<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

/* test routes*/


Route::get('/test/master', function()
{
	return View::make('landing.landing');
});

Route::get('/test/front', function()
{
	return View::make('landing.front');
});

Route::get('/test/dashboard', function()
{
	return View::make('dashboard.dashboard');
});

Route::get('/login',function(){
    return View::make('user.login');
});

Route::post('/login',array('as' => 'sign_in' , 'uses' => 'UsersController@postSignin'));

Route::get('/users/login',function() {
    return View::make('user.test');
});

Route::post('/signup',array('as' => 'sign_up','uses' => 'UsersController@postCreate'));