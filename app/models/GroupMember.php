<?php


class GroupMember extends Eloquent  {

	protected $table = 'group_members';
	protected $primaryKey = 'group_memberID';
/**********************************************************************************************/	
	public function group(){
    	return $this->belongsToMany('Group','groupID','groupID');
    }
/**********************************************************************************************/	
    
	public static function addMember($groupID, $memberID){
		$groupMember = new GroupMember;
		$groupMember->groupID = $groupID;
		$groupMember->memberID = $memberID;
		$groupMember->save();
	}	
/**********************************************************************************************/	
	public static function getGroups($memberID){
		return GroupMember::where('memberID','=',$memberID)->get();
	}
/**********************************************************************************************/	
	public static function exists($groupID, $memberID){
		$groupMember = GroupMember::where('groupID','=',$groupID)->where('memberID','=',$memberID)->get()->first();
		if($groupMember == null)// This group does not have this member 
			return false;
		else // This group already has this member
			return true;
	}
/**********************************************************************************************/	
	public static function deleteMember($groupID, $memberID){
		$groupMember = GroupMember::where('groupID','=',$groupID)
									->where('memberID','=',$memberID)->get()->first();
		if($groupMember!=null)
			$groupMember->delete();
	}
/**********************************************************************************************/	
	public static function getMembers($groupID){
		return DB::select('
        		SELECT memberID, group_members.created_at as added_at, first_name,last_name,email
				FROM group_members
				LEFT JOIN users on (group_members.memberID = users.userID)
				WHERE groupID=?
        		', array($groupID));
        }
/**********************************************************************************************/	
}