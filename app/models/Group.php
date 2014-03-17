<?php


class Group extends Eloquent  {

	protected $table = 'groups';
	protected $primaryKey = 'groupID';
/**********************************************************************************************/	
	public function groupMembers(){
		return $this->hasMany('GroupMember','groupID','groupID');
	}
/**********************************************************************************************/	
	public static function createGroup($adminID, $groupName){
		$group = new Group();
		$group->adminID = $adminID;
		$group->name = $groupName;
		$group->save();
		return $group->groupID;
	}
/**********************************************************************************************/		
	public static function exists($adminID, $groupName){
		$group = Group::where('adminID','=',$adminID)->where('name','=',$groupName)->get()->first();
		if($group== null)
			return false;// No such group exists
		else 
			return true;// A group with this name and same adminID exists
	}
/**********************************************************************************************/	
	
	
}