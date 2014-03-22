<?php

class SearchController extends BaseController {

    public $restful = true;
/**********************************************************************************************/    
	public function getSearchUsers(){
		$searchString = Input::get('searchString');
		return User::searchUser($searchString);
	}
/**********************************************************************************************/    
	public function getSearchGroups(){
		$searchString = Input::get('searchString');
		return Group::searchGroup($searchString);
	}
/**********************************************************************************************/    
	public function getSearchGroupsUsers(){//TODO

	}
/**********************************************************************************************/    
}