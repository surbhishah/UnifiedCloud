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
	public function getSearchGroupsUsers(){
		$searchString = Input::get('searchString');
		$result1 = User::searchUser($searchString)->toArray();
		//return $result1;
		$result2 = Group::searchGroup($searchString)->toArray();
		//return $result2;
		$result= array_merge_recursive($result1, $result2);
		return $result;
	}
/**********************************************************************************************/    
// TEST Function 
	public function getFilesForSearch(){
		$userID = Input::get('userID');
		$fileArray= FileModel::getFilesForSearch($userID);
		return View::make('complete')
					->with('message',$fileArray);
	}
/**********************************************************************************************/    
}