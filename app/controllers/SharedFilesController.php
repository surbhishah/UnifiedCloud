<?php

class SharedFilesController extends BaseController {

    public $restful = true;
/**********************************************************************************************/    
    public function getShareFile(){// Share a file with a user 
        try{
            $sharerEmail = Input::get('email');// email of the person with whom file is to be shared
            $sharer = User::getUserAttributes($sharerEmail,array('userID'));
            if($sharer == null){// No person with this email id is registered with our app
                return View::make('complete')->with('message','No user with the email ID :'.$sharerEmail.' 
                        is registered with our app');// TODO ABHISHEK
            }else if($sharer->userID == Session::get('userID')){
                return View::make('complete')->with('message',
                'You dont want to share a file with yourself, may be there is a mistake');// TODO ABHISHEK
            }
            else{// Person with this email exists
                $sharerID = $sharer->userID;
                $path = Input::get('path');
                $fileName = Input::get('fileName');
                $userCloudID = Input::get('userCloudID');
                $file = FileModel::getFileAttributes($userCloudID, $path, $fileName, array('fileID'));
                $fileID = $file->fileID;
                //$ownerID = UserCloudInfo::getUserID($userCloudID);
                $ownerID = Session::get('userID');
                $sharedFile=SharedFile::createSharedFile($fileID, $ownerID, $sharerID);
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
            //$ownerID = Input::get('ownerID');//UNCOMMEnt if testing from home.blade.php
            $ownerID = Session::get('userID'); 
            //return $ownerID;
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
            //$sharerID = Input::get('sharerID'); //UNCOMMEnt if testing from home.blade.php
            $sharerID = Session::get('userID');
            return SharedFile::getFilesSharedWithUser($sharerID);

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getFilesSharedWithUser");
            Log::error($e->getMessage());
            throw $e;   
        }
    }
/**********************************************************************************************/    
    public function getUnshareFile(){// Remove sharing of a file 
        try{
            $fileID = Input::get('fileID');
            $sharerID = Input::get('sharerID');
            SharedFile::removeSharing($fileID, $sharerID);

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getUnshareFile");
            Log::error($e->getMessage());
            throw $e;
        }

    }
/**********************************************************************************************/    
    public function getSharedFile(){// download a shared file 
     try{
            $sharedFileID = Input::get('sharedFileID');
            $sharedFile = SharedFile::find($sharedFileID);
            // This file has not been shared with this user, then return 
            if($sharedFile->sharerID != Session::get('userID'))
                return "This file has not been shared with you.";
            // else go ahead and download the file
            $file = $sharedFile->file()->first();
            $userCloudID = FileModel::find($file->fileID)->pluck('user_cloudID');
            $cloudID = UserCloudInfo::find($userCloudID)->pluck('cloudID');
            $cloudName = Cloud::getCloudName($cloudID);
            $cloud = parent::getCloudInstance($cloudName);
            $fileDestination=$cloud->download($file->user_cloudID, $file->path, $file->file_name);
            return Response::download($fileDestination,$file->file_name);

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getSharedFile");
            Log::error($e->getMessage());
            throw $e;
        }   
    }
/**********************************************************************************************/    
    public function getShareFileWithGroup(){
    try{
            $groupID = Input::get('groupID');
            $fileID = Input::get('fileID');//ID of the file to be shared
            $ownerID = Input::get('userID');// Comment this later 
            // $ownerID = Session::get('userID');//Uncomment this later
            $group = Group::find($groupID);
            if($group == null){
                return View::make('complete')
                            ->with('message','This group does not exist');
                        }

            $groupMembers = $group->groupMembers;
            foreach ($groupMembers as $groupMember) {
                // No need to share the file with its owner 
                if($groupMember->memberID != $ownerID){
                   SharedFile::createSharedFile($fileID, $ownerID, $groupMember->memberID);             
                }
            }

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getShareFileWithGroup");
            Log::error($e->getMessage());
            throw $e;
        }   
    }
/**********************************************************************************************/    
    public function getShareFolder(){}//TODO
/**********************************************************************************************/    
    public function getShareFolderWithGroup(){}//TODO
/**********************************************************************************************/    
    public function getUnshareFileFromGroup(){
        try{
                $groupID = Input::get('groupID');
                $fileID = Input::get('fileID');
                $group = Group::find($groupID);
                if($group == null){
                    return View::make('complete')
                            ->with('message','This group does not exist');

                }
                $groupMembers = $group->groupMembers;
                foreach ($groupMembers as $groupMember) {
                    SharedFile::removeSharing($fileID, $groupMember->memberID);
                }
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getUnshareFileFromGroup");
            Log::error($e->getMessage());
            throw $e;
        }
    }
/**********************************************************************************************/    
}
