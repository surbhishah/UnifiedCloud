<?php

class GroupsController extends BaseController {

    public $restful = true;
/**********************************************************************************************/    
    public function getCreateGroup(){// Create a new group 
     try{
            $groupName = Input::get('groupName');
            //$adminID = Session::get('userID');// Uncomment this ABHISHEK
            $adminID = Input::get('userID');// Get this from session
            // Check if user already owns a group with this name 
            if(Group::exists($adminID, $groupName)){
                return View::make('complete')  //TODO ABHISHEK
                                ->with('message','You already have a group with this name, Please choose another name');
            }
            else{
                // Create a new group
                $groupID = Group::createGroup($adminID, $groupName);
                // Add the admin of the group to the group as a member 
                GroupMember::addMember($groupID, $adminID);
            }

        }catch(Exception $e){
            Log::info("Exception raised in GroupsController::getCreateGroup");
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
            Log::info("Exception raised in GroupsController::getGroups");
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
            Log::info("Exception raised in GroupsController::postAddMember");
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
            Log::info("Exception raised in GroupsController::getMembers");
            Log::error($e->getMessage());
            throw $e;
        }
    }
/**********************************************************************************************/    
    public function deleteMember(){// delete membership of a group member 
        try{
            // only admin can delete a group member 
            // so check if the user trying to delete is the admin of the group 
            $groupID = Input::get('groupID');
            $adminID = Group::getAdminID($groupID);
            //$currentUserID = Session::get('userID');// Uncomment this later ABHISHEK
            $currentUserID = Input::get('userID');
            if($currentUserID == $adminID ){// Current user is the admin then ok 
                $groupMemberID = Input::get('groupMemberID');
                GroupMember::delete($groupMemberID);
            }else{// current user is not admin, he does not have rights to administer this group
                View::make('complete')->with('message','You are not the admin of this group , 
                    you cannot delete a member of this group')
            }
        }catch(Exception $e){
            Log::info("Exception raised in GroupsController::deleteMember");
            Log::error($e->getMessage());
            throw $e;
        }
    }
/**********************************************************************************************/    
	public function shareFileWithGroup(){
	try{
            // only admin can delete a group member 
            // so check if the user trying to delete is the admin of the group 
            $groupID = Input::get('groupID');
            $adminID = Group::getAdminID($groupID);
            //$currentUserID = Session::get('userID');// Uncomment this later ABHISHEK
            $currentUserID = Input::get('userID');
            if($currentUserID == $adminID ){// Current user is the admin then ok 
                $groupMemberID = Input::get('groupMemberID');
                GroupMember::delete($groupMemberID);
            }else{// current user is not admin, he does not have rights to administer this group
                View::make('complete')->with('message','You are not the admin of this group , 
                    you cannot delete a member of this group')
            }
        }catch(Exception $e){
            Log::info("Exception raised in GroupsController::shareFileWithGroup");
            Log::error($e->getMessage());
            throw $e;
        }	
	}
/**********************************************************************************************/    

}