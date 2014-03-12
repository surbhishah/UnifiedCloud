<?php

class UsersController extends BaseController {

    public $restful = true;
    
    public function __construct() {
      //  $this->beforeFilter('csrf', array('on'=>'post'));

        //Route filters provide a convenient way of limiting access to a given route,
        //which is useful for creating areas of your site which require authentication.
        $this->beforeFilter('auth', array('only'=>array('getDashboard')));
    }


    public function getDashboard() {
        $clouds = UserCloudInfo::getClouds(Session::get('userID'));
            //return View::make('complete')->with('message',$clouds);
        return View::make('dashboard.dashboard',array('title' => 'Dashboard','clouds' => $clouds));    
    }


    public function postSignin() {
        if (Auth::attempt(array('email'=>Input::get('email'),'password' =>Input::get('password')))) {
           
           //Session email variable to get user data from tables
            $user = User::getUserAttributes(Input::get('email'), array('userID'));
            //redundant because Auth::attempt will check this.
            if($user!=null) {
                Session::put('userID',$user->userID);
                return Redirect::route('dashboard');
            }
            else { 
                throw new Exception('Invalid Email passed to UsersController::postSignin');
                return Redirect::route('sign_in_page')
                          ->with('message', 'Incorrect email or password');
            }
        } else {
           return Redirect::route('sign_in_page')
              ->with('message', 'Incorrect email or password')
              ->withInput(Input::except('password'));
        }
    }

    public function postCreate()
    {
        $validator = Validator::make(Input::all(), User::$rules);
        if ($validator->passes()) {
    
            /*$user = new User;
            $user->first_name = Input::get('first_name');
            $user->last_name = Input::get('last_name');
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            $user->save();*/
            $firstName = Input::get('first_name');
            $lastName = Input::get('last_name');
            $email =  Input::get('email');
            $password = Input::get('password');

            $result = User::createUser($firstName,$lastName,$email,$password);
            return Redirect::route('landing')->with('message', 'Thanks for registering!');
        // validation passed, save user in DB
        } else {
            // validation failed, display error messages
            return Redirect::to('signup')
                ->with('message', 'The following errors occurred')
                ->withErrors($validator)->withInput(Input::except('password'));
        }
    }

    public function getLogout() {
        Auth::logout();
        //TODO ABHISHEK //TODO Cache clear SUrbhi
        Session::flush();
        return Redirect::route('landing')->with('message', 'Your are now logged out!');
    }
    
    //cloudName = dropbox (case insensitive)
    // userCloudName = Name of the cloud specified by user 
    public function getRegistrationPage($cloudName,$userCloudName){
             try{
            // Whenever user adds a new cloud, we first need to authenticate 
            // and get access Token from the cloud and then we will fetch full file structure of 
            // user's cloud 
                //TODO ABHISHEK
            //$userCloudName = 'surbhi';// COMMENT THIS LATER 
            $factory = new CloudFactory(); 
            $cloud = $factory->createCloud($cloudName);
            return $cloud->getRegistrationPage($userCloudName);
        
        }catch(UnknownCloudException $e){
            Log::info('UnknownCloudException raised in UsersController::getRegistrationPage',array('cloudName' => $cloudName));
            Log::error($e);

        }catch(Exception $e){
            Log::info('Exception raised in UsersController::getRegistrationPage',array('cloudName' => $cloudName));
            Log::error($e);

        }
    }

    public function getCompletion($cloudName){
        try{
            $factory = new CloudFactory(); 
            $cloud = $factory->createCloud($cloudName);
            return $cloud->getCompletion();

        }catch(UnknownCloudException $e){
            Log::info('UnknownCloudException raised in UsersController::getCompletion',array('cloudName' => $cloudName));
            Log::error($e);

        }catch(Exception $e){
            Log::info('Exception raised in UsersController::getCompletion',array('cloudName' => $cloudName));
            Log::error($e);

        }
    }        
    // todo this is a development function...to be deleted later 
    public function getHome(){
        return View::make('home');
    }

}
