<?php


class GroupMember extends Eloquent  {

	protected $table = 'group_members';
	protected $primaryKey = 'group_memberID';
	
	public function group(){
    	return $this->belongsToMany('Group','groupID','groupID');
    }
    
	public static function addMember($groupID, $memberID){
		$groupMember = new GroupMember;
		$groupMember->groupID = $groupID;
		$groupMember->memberID = $memberID;
		$groupMember->save();
	}
		
	public static function getGroups($memberID){
		return GroupMember::where('memberID','=',$memberID)->get();
	}

	public static function exists($groupID, $memberID){
		$groupMember = GroupMember::where('groupID','=',$groupID)->where('memberID','=',$memberID)->get()->first();
		if($groupMember = null)// This group does not have this member 
			return false;
		else // This group already has this member
			return true;
	}
	
}