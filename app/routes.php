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

/*
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

// Route::get('/login',function(){
//     return View::make('user.login');
// });

Route::post('user/login',array('as' => 'sign_in' , 'uses' => 'UsersController@postSignin'));

Route::get('user/login',function() {
    return View::make('landing.landing');
});

Route::get('user/logout', array('as' => 'logout' , 'uses' => 'UsersController@getLogout'));

// Route::get('/users/dashboard',function() {
//     return View::make('dashboard.dashboard');
// });

Route::get('user/dashboard', array('as' => 'dashboard', 'uses' => 'UsersController@getDashboard'));
Route::post('signup',array('as' => 'sign_up','uses' => 'UsersController@postCreate'));

Route::get('user/register',array('as' => 'register', 'uses' => 'UsersController@getRegister'));



// UNCOMMENT these two routes when you need authentication of dropbox 
// I have disabled this functionality after getting accessToken for my dropbox account 
// and hardcoding it in the seeds 


//Route::get('user/{cloud}',array('as'=>'authenticate', 'uses'=>'UsersController@getRegistrationPage'));

Route::get('authenticate',array(
	'as'=>'authenticate_route',
	'uses'=>'UsersController@getRegistrationPage'
	));


Route::get('auth/dropbox',array(
	'as'=>'completion_route',
	'uses'=>'UsersController@getCompletion'
	));
											// home page
Route::get('user/home',array(
	'as'=>'home_route',
	'uses'=>'UsersController@getHome'
));

											// download
/*Route::get('user/download/{cloudName}/{cloudSourcePath}/{fileName}',array(
	'as'=>'download_route',
	'uses'=>'FilesController@getFile'
));*/
Route::get('user/download',array(
	'as'=>'download_route',
	'uses'=>'FilesController@getFile'
));
											//upload
Route::post('user/upload/{cloudName}',array(
	'as'=>'upload_route',
	'uses'=>'FilesController@postFile'
));

											// get folder content
Route::get('user/folder_content',array(
	'as'=>'folder_content_route',
	'uses'=>'FilesController@getFolderContents'
));

												// create folder 
Route::get('user/new_folder/{cloudName}/{folderPath}',array(
	'as'=>'create_folder_route',
	'uses'=>'FilesController@getCreateFolder'
));

												// delete a file or folder 
Route::delete('user/delete/{cloudName}/{folderPath}',array(
	'as'=>'delete_route',
	'uses'=>'FilesController@delete'
));
												//add a new cloud 
Route::get('user/add_cloud/{cloudName}',array(
	'as'=>'add_cloud_route',
	'uses'=>'FilesController@getAddCloud'
));
												//refresh cloud
Route::get('user/refresh/{cloudName}',array(
	'as'=>'refresh_route',
	'uses'=>'FilesController@getRefresh'
));




/// This is a test route 
Route::get('user/download_folder',array(
	'as'=>'download_folder_route',
	'uses'=>'FilesController@getDownloadFolder'
));

