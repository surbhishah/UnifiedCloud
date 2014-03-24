<?php

// use repositories\UserRepositoryInterface as User;

class AutosyncController extends BaseController {

    public $restful = true;

    public function getUpdate($userID) {
        try{
            $clouds = UserCloudInfo::getClouds($userID);
            return Response::json($clouds); 
        
        }catch(Exception $e){
                Log::info("Exception raised in AutosyncController::getUpdate");
                Log::error($e->getMessage());
                throw $e;
        }
    }
    public function postFile($cloudName, $userCloudID){
        try{
            //$cloudName = Input::get('cloudName');
            //$userCloudID = Input::get('userCloudID');       
            $cloudDestinationPath = Input::get('cloudDestinationPath');
            $factory = new CloudFactory(); 
            $cloud = $factory->createCloud($cloudName);
            $file = Input::file('file');
            $result= $cloud->upload($userCloudID, $file, $cloudDestinationPath);    
            return Response::json($result);
                
        }catch(UnknownCloudException $e){
                Log::info("UnknownCloudException raised in AutosyncController::postFile");
                Log::error($e->getMessage());
                throw $e;

        }catch(Exception $e){
                Log::info("Exception raised in AutosyncController::postFile");
                Log::error($e->getMessage());
                throw $e;
        }   
            
    }
    
    public function getCreateFolder($cloudName, $userCloudID){
        try{
        
            $folderPath= Input::get('folderPath');
            $factory = new CloudFactory(); 
            $cloud = $factory->createCloud($cloudName);
            $result = $cloud->createFolder($userCloudID, $folderPath);
            //if $result == null then folder already exists
            return Response::json($result);

        }catch(UnknownCloudException $e){
                Log::info("UnknownCloudException raised in AutosyncController::getCreateFolder");
                Log::error($e->getMessage());
                throw $e;

        }catch(Exception $e){
                Log::info("Exception raised in AutosyncController::getCreateFolder");
                Log::error($e->getMessage());
                throw $e;
        }
    }

    public function delete($cloudName, $userCloudID){
        try{
            $path= Input::get('path');
            $factory = new CloudFactory(); 
            $cloud = $factory->createCloud($cloudName);
            $result = $cloud->delete($userCloudID,$path);
            return Response::json($result);

        }catch(UnknownCloudException $e){
                Log::info("UnknownCloudException raised in AutosyncController::delete");
                Log::error($e->getMessage());
                throw $e;

        }catch(Exception $e){
                Log::info("Exception raised in AutosyncController::delete");
                Log::error($e->getMessage());
                throw $e;
        }
    }
}