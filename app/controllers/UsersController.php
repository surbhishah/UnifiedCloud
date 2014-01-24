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
        
        $clouds = UnifiedCloud::getCloudsByEmail(Session::get('email'));
        return View::make('dashboard.dashboard')->with('clouds',$clouds);    
    }

    public function postSignin() {
        if (Auth::attempt(array('email'=>Input::get('email'),'password' =>Input::get('password')))) {
           
           //Session email variable to get user data from tables
            Session::put('email',Input::get('email'));
            $clouds = UnifiedCloud::getCloudsByEmail(Session::get('email'));

            return View::make('dashboard.dashboard')->with('clouds',$clouds);
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
    
            $user = new User;
            $user->first_name = Input::get('first_name');
            $user->last_name = Input::get('last_name');
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            $user->save();

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
        return Redirect::route('landing')->with('message', 'Your are now logged out!');
    }
    
    //cloudName = dropbox (case insensitive)
    // userCloudName = Name of the cloud specified by user 
    public function getRegistrationPage($cloudName,$userCloudName){
             try{
            // Whenever user adds a new cloud, we first need to authenticate 
            // and get access Token from the cloud and then we will fetch full file structure of 
            // user's cloud 
            $userCloudName = 'surbhi';
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
            // Whenever user adds a new cloud, we first need to authenticate 
            // and get access Token from the cloud and then we will fetch full file structure of 
            // user's cloud 
            // This function has been made only to check the functionality of getFullFileStructure
            // It may not be required later 
            // At present, I have hard coded the access token in the database 
                
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
