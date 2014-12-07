<?php

class SearchController extends BaseController {

    public $restful = true;
/**********************************************************************************************/    
	public function getSearchUsers(){
		$searchString = Input::get('query');
		return Response::json(User::searchUser($searchString));
	}
/**********************************************************************************************/    
	public function getSearchGroups(){
		$searchString = Input::get('query');
		return Group::searchGroup($searchString);
	}
/**********************************************************************************************/    
	public function getSearchGroupsUsers(){
		$searchString = Input::get('query');
		$result1 = User::searchUser($searchString)->toArray();
		//return $result1;
		$result2 = Group::searchGroup($searchString)->toArray();
		//return $result2;
		$result= array_merge_recursive($result1, $result2);
		return Response::json($result);
	}
/**********************************************************************************************/    
// TEST Function 
	public function getFilesForSearch(){
		//$userID = Input::get('userID');
		$userID = Session::get('userID');
		$query = Input::get("query");
		$fileArray= FileModel::getFilesForSearch($userID);
		$searchedFileArray = array();
		foreach ($fileArray as $file) {
			$distance = Utility::levenschtein_search($file['file_name'],$query);
			Log::info("distance between: ",array('file',$file['file_name'],'query',$query,'distance',$distance));
			if($distance < 2) {
				$searchedFileArray = array_merge($searchedFileArray,$file);
			}
		}
		return Response::json($fileArray);
		//return View::make('complete')->with('message',$fileArray);
	}
/**********************************************************************************************/    
	public function getFileDetailsForFileID($fileID){
		//$userID = Input::get('userID');
		// $userID = Session::get('userID');
		 Log::info('fileID in SearchController',array('fileID',$fileID));
		$fileArray = FileModel::getFileDetails($fileID);
		return $fileArray;
		//return View::make('complete')->with('message',$fileArray);
	}
/**********************************************************************************************/    
	
	 
}