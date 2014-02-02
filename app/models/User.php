<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {
	/**
	 * These rules are used by Validator.
	 *
	 * @var array
	 */
	public static $rules = array(
			'first_name'=>'required|alpha|min:1',
			'last_name'=>'required|alpha|min:1',
			'email'=>'required|email|unique:users',
			'password'=>'required|alpha_num|between:6,20|confirmed',
			'password_confirmation' => 'required|alpha_num|between:6,20'
	);
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	
	protected $table = 'users';
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');
	protected $primaryKey = 'userID';

	public function filesSharedByUser(){// NOT used for now 
        return $this->hasMany('SharedFile', 'ownerID' , 'userID');
    }
	public function filesSharedWithUser(){// NOT used for now
		return $this->hasMany('SharedFile', 'sharerID', 'userID');
	}
/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/*
	*	@params:
	*		firstname : string
	*		lastname : string
	*		email : email of the user ..email is the email with our app
	*		password : user password for this app , string
	*
	*	@desc: create new user
	*/
	public static  function createUser($firstName,$lastName,$email,$password) {

		$user = new User;
		$user->first_name = $firstName;
		$user->last_name =	$lastName;
		$user->email = $email;
		$user->password = Hash::make($password);
		$user->save();

	}
	/*
	*	@params:
	*		email : email of the user ..email is the email with our app
	*		attributes: array of attributes to be returned
	*	@return value:
	*	 	Boolean : userID of the user with this email
	*/

	public static function getUserAttributes($email,$attributes) {
		//user cannot sign in without a valid email id, therefore no checking for
		//validity of email.
		return User::where('email','=',$email)->select($attributes)->get()->first();
	}
	/*
	*	@params:
	*		uid: unique id returned by dropbox, it uniquely identifies a user of dropbo
	*		cloudID: ID of the cloud
	*	@return value:
	*	 	Boolean : true if user with same uid and cloudID exists
	*				  false if user with same uid and cloudID does not exist
	*/
	public static function userAlreadyExists($uid, $cloudID){
		$userCloudInfo= UserCloudInfo::where('uid','=',$uid)->where('cloudID','=',$cloudID)->get()->first();
		if($userCloudInfo==null)return false;
		else return true;
	}
	//TODO
	public static function getFilesSharedByUser($ownerID){
		return User::find($ownerID)->filesSharedByUser->file;
	}
	
}
