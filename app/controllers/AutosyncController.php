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
            $i=0;// TO BE COMMENTED GEtting $result is not necessary ,,,delete it later abhishek
            $files = Input::file('files');
            foreach($files as $file){
                $result[$i]= $cloud->upload($userCloudID, $file, $cloudDestinationPath);    
                $i++;
            }
            return $result;//THIS is required only for testing
                
        }catch(UnknownCloudException $e){
                Log::info("UnknownCloudException raised in FilesController::postFile");
                Log::error($e->getMessage());
                throw $e;

        }catch(Exception $e){
                Log::info("Exception raised in FilesController::postFile");
                Log::error($e->getMessage());
                throw $e;
        }   
            
    }
    
    public function getCreateFolder(){

    }


}