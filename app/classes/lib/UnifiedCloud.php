<?php
class UnifiedCloud {

	public static function addFileInfo($fileName, $userID, $cloudID, 
		$path,  $isDirectory, $lastModifiedTime, $size,$rev,$isEncrypted=false){

		$file = new FileModel();
		$file->path = $path;
		$file->is_encrypted=$isEncrypted;
		$file->userID = $userID;
		$file->cloudID = $cloudID;
		$file->file_name = $fileName;
		$file->last_modified_time= $lastModifiedTime;
		$file->is_directory= $isDirectory;
		$file->rev = $rev;
		$file->size = $size ;
		$file->save();

	}
	
	public static function getAccessToken( $userID,$cloudID){
		return UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->pluck('access_token');		
	}
	
	public static function getFile($userID, $cloudID, $path, $fileName){
			return FileModel::where('userID','=',$userID)->where('cloudID','=',$cloudID)->where('path','=',$path)->where('file_name','=',$fileName)->get()->first();
	}
//TODO
	public static function addOrUpdateFileInfo($fileName, $userID, $cloudID, 
		$path,  $isDirectory, $lastModifiedTime, $size,$rev,$isEncrypted=false){

		$file = FileModel::where('userID','=',$userID)->where('cloudID','=',$cloudID)->where('path','=',$path)->where('file_name','=',$fileName)->get()->first();
		if($file == null){
			UnifiedCloud::addFileInfo($fileName, $userID, $cloudID, 
			$path,  $isDirectory, $lastModifiedTime, $size,$rev,$isEncrypted);

		}
		else{
	//		$file 
		}
	}


	public static function addTempEntry($fileID){
		$temp = new Temp();
		$temp->fileID = $fileID;
		$temp->save();
	}
	public static function TempFileExists($fileID){
		$file = Temp::where('fileID','=',$fileID)->get()->first();
		if($file ==NULL )return false;
		else return true;
	}
	


	public static function resetFileState($userID, $cloudID){
		FileModel::where('userID','=',$userID)->where('cloudID','=',$cloudID)->delete();
	}
	


	public static function getOldCursor($userID, $cloudID){
		return UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->get()
																		->first()->pluck('cursor');
	}

	public static function setNewCursor($userID, $cloudID, $cursor){

		$userCloudInfo= UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)
																				->get()->first();
		$userCloudInfo->cursor = $cursor;	
		$userCloudInfo->save();

	}



}















