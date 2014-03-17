<?php

class SharedFilesController extends BaseController {

    public $restful = true;
/**********************************************************************************************/    
    public function getShareFile(){// Share a file with a user 
        try{
            $sharerEmail = Input::get('sharerEmail');// email of the person with whom file is to be shared
            $sharer = User::getUserAttributes($sharerEmail,array('userID'));
            if($sharer == null){// No person with this email id is registered with our app
                return View::make('complete')->with('message','No user with the email ID :'.$sharerEmail.' 
                        is registered with our app');// TODO ABHISHEK
            }
            else{// Person with this email exists
                $sharerID = $sharer->userID;
                $path = Input::get('path');
                $fileName = Input::get('fileName');
                $userCloudID = Input::get('userCloudID');
                $file = FileModel::getFileAttributes($userCloudID, $path, $fileName, array('fileID'));
                $fileID = $file->fileID;
                $ownerID = UserCloudInfo::getUserID($userCloudID);
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
            $ownerID = Input::get('ownerID');//UNCOMMEnt if testing from home.blade.php
        //  $ownerID = Session::get('ownerID'); 
            return User::find($ownerID)->filesSharedByUser;
            
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getFilesSharedByUser");
            Log::error($e->getMessage());
            throw $e;   
        }
    }
/**********************************************************************************************/    
    public function getFilesSharedWithUser(){
        try{
            $sharerID = Input::get('sharerID'); //UNCOMMEnt if testing from home.blade.php
     //     $sharerID = Input::get('sharerID');  
            return User::find($sharerID)->filesSharedWithUser;

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getFilesSharedWithUser");
            Log::error($e->getMessage());
            throw $e;   
        }
    }
/**********************************************************************************************/    
    public function getUnshareFile(){// Remove sharing of a file 
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
    public function getCreateGroup(){// Create a new group 
     try{
            $groupName = Input::get('groupName');
            //$adminID = Session::get('userID');// Uncomment this ABHISHEK
            $adminID = Input::get('userID');// Get this from session
            // Check if user already owns a group with this name 
            if(Group::exists($adminID, $groupName)){
                return View::make('complete')  
                                ->with('message','You already have a group with this name, Please choose another name');
            }
            else{
                // Create a new group
                $groupID = Group::createGroup($adminID, $groupName);
                // Add the admin of the group to the group as a member 
                GroupMember::addMember($groupID, $adminID);
            }

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getCreateGroup");
            Log::error($e->getMessage());
            throw $e;
        }   
    }
/**********************************************************************************************/    
    public function getGroups(){//TODO Return complete data 
        try{
            //$adminID = Session::get('userID');// Uncomment this ABHISHEK
            $userID = Input::get('userID');// Get this frm session
            return GroupMember::getGroups($userID);
        
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getGroups");
            Log::error($e->getMessage());
            throw $e;
        }   

    }
/**********************************************************************************************/    
    public function postAddMember(){// Add new member to a group 
     try{
            $newMemberEmail = Input::get('newMemberEmail');
            $groupID = Input::get('groupID');
            $newMember = User::getUserAttributes($newMemberEmail, array('userID'));
            if($newMember == null)
                return "User with email ".$newMemberEmail." does not seem to have an account with us.";
        
            if(GroupMember::exists($groupID, $newMemberID))
                return "User with email ".$newMemberEmail." already exists in this group.";
            else
                return GroupMember::addMember($groupID, $newMemberID);// This function need not return anything
        
        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::postAddMember");
            Log::error($e->getMessage());
            throw $e;
        }   

    }
/**********************************************************************************************/    
    public function getMembers(){// Get members of a group //TODO,,more information regarding members
        try{
            $groupID = Input::get('groupID');
            $group = Group::find($groupID);
            if($group == null)
                return "This group does not exist";
            else
                return $group->groupMembers;        

        }catch(Exception $e){
            Log::info("Exception raised in SharedFilesController::getMembers");
            Log::error($e->getMessage());
            throw $e;
        }
    }
/**********************************************************************************************/    
}
