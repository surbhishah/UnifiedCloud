<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	public static $rules = array(
			'first_name'=>'required|alpha|min:1',
			'last_name'=>'required|alpha|min:1',
			'emailID'=>'required|email|unique:users',
			'password'=>'required|alpha_num|between:6,20|confirmed',
			'password_confirmation' => 'required|alpha_num|between:6,20'
		);


	/**
	 * These rules are used by Validator.
	 *
	 * @var array
	 */
	public static $rules = array(
         'first_name' => 'required|alpha|min:3',
        'last_name' => 'required|alpha|min:3',
        'email' => 'required|email|unique:users',
    'password' => 'required|alpha_num|between:6,12|confirmed'
        // 'password_confirmation' => 'required|alpha_num|between:6,12'
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
		return $this->emailID;
	}

}
