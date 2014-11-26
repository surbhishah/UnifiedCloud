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

App::bind('UserRepositoryInterface','EloquentUserRepository');
//test routes
Route::get('mock',function(){
	return View::make('dashboard.dashboardMock');
});

Route::post('user/test',array(
	'as' => 'upload_with_encryption_test',
	'uses' => 'EncryptionController@postTestGet'
));

//test route for home.php

Route::get('/home',function(){
	return View::make('hello');
});

// open front page 
Route::get('/', array( 'as' => 'landing', function()
{
	return View::make('landing.landing',array('title' => 'Kumo.'));
}));

//user login
Route::post('user/login',array(
	'as' => 'sign_in' , 
	'uses' => 'UsersController@postSignin'
	));

//user signup
Route::post('user/signup',array(
	'as' => 'sign_up',
	'uses' => 'UsersController@postCreate'
	));


Route::get('user/register',array(
	'as' => 'register', 
	'uses' => 'UsersController@getRegister'
	));


//user signin 
Route::get('signin', array( 
	'as' => 'sign_in_page',function() {
    return View::make('user.signin',array('title' => 'Sign in.'));
}));

//user signup
Route::get('signup', array(
	'as' => 'sign_up_page',function() {
    return View::make('user.signup',array('title' => 'Sign up.'));
}));

//logout
Route::get('user/logout', array(
	'as' => 'logout' , 
	'uses' => 'UsersController@getLogout'
	));

Route::get('user/dashboard', array(
	'as' => 'dashboard', 
	'uses' => 'UsersController@getDashboard'
	));


// Route to user authentication
Route::get('user/authenticate/{cloudName}/{userCloudName}',array(
	'as'=>'authenticate_route',
	'uses'=>'UsersController@getRegistrationPage'
	));

//authorization from dropbox
Route::get('auth/{cloudName}',array(
	'as'=>'completion_route',
	'uses'=>'UsersController@getCompletion'
	));

// home page...This is a development route..to be deleted later 
Route::get('user/home',array(
	'as'=>'home_route',
	'uses'=>'UsersController@getHome'
));

// Get full file structure 
Route::get('user/get_full_file_structure',array(
	'as'=>'get_full_file_structure_route',
	'uses'=>'FilesController@getFullFileStructure'
));
// download
Route::get('user/download',array(
	'as'=>'download_route',
	'uses'=>'FilesController@getFile'
));

//download encrypted file after decryption
Route::post('user/downloadEncryptedFile',array(
	'as' => 'download_encrypted_file',
	'uses' => 'EncryptionController@postDownloadEncryptedFile'
));

//upload any no of files 
Route::post('user/upload/{cloudName}',array(
	'as'=>'upload_route',
	'uses'=>'FilesController@postFile'
));

//upload any no of files and encrypt them
Route::post('user/uploadWithEncryption/{cloudName}',array(
	'as' => 'upload_with_encryption',
	'uses' => 'EncryptionController@postEncryptFiles'
	));

// get folder content
Route::get('user/folder_content',array(
	'as'=>'folder_content_route',
	'uses'=>'FilesController@getFolderContents'
));

// create folder 
Route::get('user/new_folder',array(
	'as'=>'create_folder_route',
	'uses'=>'FilesController@getCreateFolder'
));

// delete a file or folder 
Route::delete('user/delete',array(
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
//download folder route 
Route::get('user/download_folder',array(
	'as'=>'download_folder_route',
	'uses'=>'FilesController@getDownloadFolder'
));

//share file route
Route::get('user/share',array(
	'as'=>'share_file_route',
	'uses'=>'SharedFilesController@getShareFile'
));

// get files shared by user
Route::get('user/get_files_shared_by_user',array(
	'as'=>'files_shared_by_user_route',
	'uses'=>'SharedFilesController@getFilesSharedByUser'
));

// get files shared with user
Route::get('user/get_files_shared_with_user',array(
	'as'=>'files_shared_with_user_route',
	'uses'=>'SharedFilesController@getFilesSharedWithUser'
));

//download shared file route 
Route::get('user/download_shared_file',array(
	'as'=>'download_shared_file_route',
	'uses'=>'SharedFilesController@getSharedFile'
));
// Change access RIghts
Route::get('user/change_access_rights',array(
	'as'=>'change_access_rights_route',
	'uses'=>'SharedFilesController@getChangeAccessRights'
));

// Create group 
Route::get('user/group/create',array(
	'as'=>'create_group_route',
	'uses'=>'GroupsController@getCreateGroup'
));
// delete group 
Route::delete('user/group/delete',array(
	'as'=>'delete_group_route',
	'uses'=>'GroupsController@deleteGroup'
));
// Get groups
Route::get('user/group/get',array(
	'as'=>'get_groups_route',
	'uses'=>'GroupsController@getGroups'
));
// Add member to group 
Route::post('user/group/add',array(
	'as'=>'add_member_route',
	'uses'=>'GroupsController@postAddMember'
));

// Get group members 
Route::get('user/group/getMembers',array(
	'as'=>'get_group_members_route',
	'uses'=>'GroupsController@getMembers'
));


// delete group member 
Route::delete('user/group/deleteMember',array(
	'as'=>'delete_group_member_route',
	'uses'=>'GroupsController@deleteMember'
));

// Search user
Route::get('user/user_group/search',array(
	'as'=>'search_user_group_route',
	'uses'=>'SearchController@getSearchGroupsUsers'
));
// Share with group route
Route::get('user/group/share',array(
	'as'=>'share_with_group_route',
	'uses'=>'SharedFilesController@getShareFileWithGroup'
));
// UnShare with group route
Route::get('user/group/unshare',array(
	'as'=>'unshare_group_route',
	'uses'=>'SharedFilesController@getUnshareFileFromGroup'
));
/****************************************************************************/
// Autosync routes
//add a new cloud 
Route::get('remote/update_clouds/{userID}',array(
	'as'=>'remote_update_clouds',
	'uses'=>'AutosyncController@getUpdate'
));
//upload any no of files 
Route::post('remote/upload/{cloudName}/{userCloudID}',array(
	'as'=>'remote_upload_route',
	'uses'=>'AutosyncController@postFile'
));
//create folder
Route::get('remote/create_folder/{cloudName}/{userCloudID}',array(
	'as'=>'remote_create_folder_route',
	'uses'=>'AutosyncController@getCreateFolder'
));
//delete folder/ file 
Route::get('remote/delete/{cloudName}/{userCloudID}',array(
	'as'=>'remote_delete_folder_route',
	'uses'=>'AutosyncController@delete'
));

//Dummy route// to be deleted , just to check if all files are returned 
// correctly 
Route::get('user/search/files',array(
	'as'=>'search_files_route',
	'uses'=>'SearchController@getFilesForSearch'
));

Route::get('user/search/file/{fileID}',array(
	'as'=>'search_file',
	'uses'=>'SearchController@getFileDetailsForFileID'
));
Route::get('user/getClient/{userCloudID}/{cloudName}',array(
	'as'=>'testclient',
	'uses'=>'HomeController@getClient'
));
