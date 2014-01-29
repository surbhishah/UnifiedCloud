<?php

class SharedFilesController extends BaseController {

    public $restful = true;

    public function getShareFile(){
        try{
            $sharerEmail = Input::get('sharerEmail');// email of the person with whom file is to be shared
            $sharer = User::getUserAttributes($sharerEmail,array('userID'));
            if($sharer == null){// No person with this email id is registered with our app
                return View::make('complete')->with('message','No user with the email ID :'.$sharerEmail.' is registered with our app');// TODO ABHISHEK
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

    public function getFilesSharedByUser(){
        //$ownerID = Session::get('userID'); // UNCOMMENT THIS
        try{
            $ownerID = '1';//TO BE COMMENTED GET USERID FROM SESSION
            return SharedFile::getFilesSharedByUser($ownerID);
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getFilesSharedByUser");
            Log::error($e->getMessage());
            throw $e;   
        }
    }
    public function getFilesSharedWithUser(){
    //$sharerID = Session::get('userID'); // UNCOMMENT This
        try{
            $sharerID = '1';//TO BE COMMENTED GET USERID FROM SESSION
            return SharedFile::getFilesSharedWithUser($sharerID);
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getFilesSharedWithUser");
            Log::error($e->getMessage());
            throw $e;   
        }
    }
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
    public function getChangeAccessRights(){
        try{
            $sharedFileID = Input::get('sharedFileID');
            $sharerID =  Input::get('sharerID');
            

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getChangeAccessRights");
            Log::error($e->getMessage());
            throw $e;
        }
    }
    public function getCreateGroup(){
        //TODO Surbhi
    }
    
}
