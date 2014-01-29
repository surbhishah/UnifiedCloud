<?php

class SharedFilesController extends BaseController {

    public $restful = true;
/**********************************************************************************************/    
    public function getShareFile(){
        try{
            $sharerEmail = Input::get('sharerEmail');// email of the person with whom file is to be shared
            $sharer = User::getUserAttributes($sharerEmail,array('userID'));
            if($sharer == null){// No person with this email id is registered with our app
                return View::make('complete')->with('message','No user with the email ID :'.$sharerEmail.' 
                        is registered with our app');// TODO ABHISHEK
            }
            else{// Person with this email exists
                $sharerID = $sharer->userID;
                $accessRights = Input::get('accessRights');
                $path = Input::get('path');
                $fileName = Input::get('fileName');
                $userCloudID = Input::get('userCloudID');
                $file = FileModel::getFileAttributes($userCloudID, $path, $fileName, array('fileID'));
                $fileID = $file->fileID;
                $ownerID = UserCloudInfo::getUserID($userCloudID);
                $sharedFile=SharedFile::createSharedFile($fileID, $ownerID, $sharerID, $accessRights);
                // COMMENT THIS LATER 
                return $sharedFile;// This function does not return anything ...This return is just for testing                 
            }
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getShareFile");
            Log::error($e->getMessage());
            throw $e;
        }
    }
/**********************************************************************************************/    
    public function getFilesSharedByUser(){
        try{
        //    $ownerID = Input::get('ownerID');//UNCOMMEnt if testing from home.blade.php
          $ownerID = Session::get('userID'); 
          return SharedFile::getFilesSharedByUser($ownerID);
            
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getFilesSharedByUser");
            Log::error($e->getMessage());
            throw $e;   
        }
    }
/**********************************************************************************************/    
    public function getFilesSharedWithUser(){
        try{
    //        $sharerID = Input::get('sharerID'); //UNCOMMEnt if testing from home.blade.php
            $sharerID = Session::get('userID'); 
            return SharedFile::getFilesSharedWithUser($sharerID);
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getFilesSharedWithUser");
            Log::error($e->getMessage());
            throw $e;   
        }
    }
/**********************************************************************************************/    
    public function getUnshareFile(){
        try{
            $sharedFileID = Input::get('sharedFileID');
            SharedFile::removeSharedFile($sharedFileID);

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getUnshareFile");
            Log::error($e->getMessage());
            throw $e;
        }

    }
/**********************************************************************************************/    
    public function getChangeAccessRights(){
        try{
            $sharedFileID = Input::get('sharedFileID');
            $accessRights = Input::get('accessRights');
            SharedFile::setAccessRights($sharedFileID, $accessRights);

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getChangeAccessRights");
            Log::error($e->getMessage());
            throw $e;
        }
    }
/**********************************************************************************************/    
    public function getSharedFile(){// download a shared file 
     try{
            $sharedFileID = Input::get('sharedFileID');
            $file = SharedFile::getFile($sharedFileID);
            $cloudID = UserCloudInfo::find($file->user_cloudID)->pluck('cloudID');
            $cloudName = Cloud::getCloudName($cloudID);
            $factory = new CloudFactory(); 
            $cloud = $factory->createCloud($cloudName);
            $fileDestination=$cloud->download($file->user_cloudID, $file->path, $file->file_name);
            return Response::download($fileDestination,$file->file_name);

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getSharedFile");
            Log::error($e->getMessage());
            throw $e;
        }   
    }
/**********************************************************************************************/    
    public function getCreateGroup(){
        //TODO Surbhi
    }
/**********************************************************************************************/    
    public function postSharedFile(){
        //$userID = Session::get('userID');
        //TODO SURBHI
    }
/**********************************************************************************************/    
}
