<?php

class SharedFilesController extends BaseController {
//TODO
    public $restful = true;
    
    public function getShareFile(){
        try{
            $sharerEmail = Input::get('sharerEmail');// email of the person with whom file is to be shared
            $sharer = User::getUser('sharerEmail');
            if($sharer == null){// No person with this email id is registered with our app
                return View::make('complete')->with('message','No user with the email ID :'.$sharerEmail.'is registered with our app');// TODO ABHISHEK
            }
            else{// Person with this email exists
                $sharerID = $sharer->userID;
                $accessRights = Input::get('accessRights');
                $path = Input::get('path');
                $fileName = Input::get('fileName');

                
            }
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getShareFile");
            Log::error($e->getMessage());
            throw $e;
        }
    }
    public function getUnshareFile(){
        try{

        }catch(){
            Log::info("Exception raised in SharedFilesController::getShareFile");
            Log::error($e->getMessage());
            throw $e;

        }

    }

    public function getCreateGroup(){

    }
    
}
