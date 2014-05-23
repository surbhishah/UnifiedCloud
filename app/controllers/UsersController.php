<?php

// use repositories\UserRepositoryInterface as User;

class UsersController extends BaseController {

    public $restful = true;
    protected $user;
    /**
     * dependency injection for model using repositories.
     * Repositories are classes abstracting models from controllers
     * so that we do not need to make calls to Eloquent ORM, instead
     * the model interface has now become replaceable which allows
     * to write tests using Mockery and also allows us to replace ORM if required.  
     */
    public function __construct(UserRepositoryInterface $user) {
        
        $this->user = $user;
        $this->beforeFilter('csrf', array('on'=>'post'));

        //Route filters provide a convenient way of limiting access to a given route,
        //which is useful for creating areas of your site which require authentication.
        //$this->beforeFilter('auth', array('only'=>array('getDashboard')));
    }

    public function getDashboard() {
        $clouds = UserCloudInfo::getClouds(Session::get('userID'));
            //return View::make('complete')->with('message',$clouds);
        return View::make('dashboard.dashboard',array('title' => 'Dashboard','clouds' => $clouds));    
    }


    public function postSignin() {
        if (Auth::attempt(array('email'=>Input::get('email'),'password' =>Input::get('password')))) {
           
           //Session email variable to get user data from tables
            $user = $this->user->getUserAttributes(Input::get('email'), array('userID'));
            var_dump($user['userID']);
            Session::put('userID',$user['userID']);
            return Redirect::route('dashboard');
           
        } else {
           return Redirect::route('sign_in_page')
              ->with('message', 'Incorrect email or password')
              ->withInput(Input::except('password'));
        }
    }

    public function postCreate()
    {
        $validator = Validator::make(Input::all(), User::$rules);
        if (!$validator->fails()) {
    
            $firstName = Input::get('first_name');
            $lastName = Input::get('last_name');
            $email =  Input::get('email');
            $password = Input::get('password');

            $result = $this->user->createUser($firstName,$lastName,$email,$password);
            return Redirect::route('landing')->with('message', 'Thanks for registering!');
        // validation passed, save user in DB
        } else {
            // validation failed, display error messages
            return Redirect::route('sign_up')
                ->with('message', 'The following errors occurred')
                ->withErrors($validator)->withInput(Input::except('password'));
        }
    }

    public function getLogout() {
        Auth::logout();
        //TODO ABHISHEK //TODO Cache clear SUrbhi
        Session::flush();
        return Redirect::route('landing')->with('message', 'You are now logged out!');
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
