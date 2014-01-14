<?php

class UsersController extends BaseController {

    public function __construct() {
      //  $this->beforeFilter('csrf', array('on'=>'post'));

        //Route filters provide a convenient way of limiting access to a given route,
        //which is useful for creating areas of your site which require authentication.
        $this->beforeFilter('auth', array('only'=>array('getDashboard')));
    }

    public function postSignin() {
        if (Auth::attempt(array('email'=>Input::get('email'),'password' =>Input::get('password')))) {
           return Redirect::to('users/dashboard')->with('message', 'You are now logged in!');
        } else {
           return Redirect::to('users/login')
              ->with('message', 'Your username/password combination was incorrect');
              //->withInput();
        }
    }

    public function postCreate()
    {
        // $validator = Validator::make(Input::all(), User::$rules);

        // if ($validator->passes()) {
    if(1){
            $user = new User;
            $user->firstname = Input::get('firstname');
            $user->lastname = Input::get('lastname');
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            $user->save();

            return Redirect::to('users/login')->with('message', 'Thanks for registering!');
        // validation has passed, save user in DB
        } else {
            // validation has failed, display error messages
            return Redirect::to('users/register')
                ->with('message', 'The following errors occurred')
                ->withErrors($validator)->withInput();
        }
    }
}
