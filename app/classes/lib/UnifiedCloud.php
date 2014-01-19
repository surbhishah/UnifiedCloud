<?php
class UnifiedCloud {
/**********************************************************************************************/
	/*
	*	@params:
	*		fileName = Name of the file along with extension Eg : file.txt 
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*		path = Path to the file For eg: /Project/UniCloud for /Project/UniCloud/file.txt
	*		isDirectory = true if the file is actually a directory otherwise false
	*		lastModifiedTime=  Last Modified Time in YYYY-MM-DD HH:MM:SS format ie the database format
	*		size = size of the file
	*		rev = revision of the file 
	*		isEncrypted = is this parameter is not passed , the default value of false will be taken
	*	@return value:
	*	 	None
	*	@decription : Adds a new file to UnifiedCloud database 
	*
	*/
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
/**********************************************************************************************/
	/*
	*	@params:
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*	@return value:
	*	 	string: access_token
	*	@decription : Returns access Token of a user 
	*
	*/
	public static function getAccessToken( $userID,$cloudID){
		return UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->pluck('access_token');		
	}
/**********************************************************************************************/	
	/*
	*	@params:
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*		path : Path to the file For eg /Project/UniCloud for a file at /Project/UniCloud/file.txt
	*		fileName:	Name of the file For eg file.txt
	*	@return value:
	*	 	object of class FileModel
	*	@decription : Returns the file of a user at a particular path on the cloud 
	*
	*/
	public static function getFile($userID, $cloudID, $path, $fileName){
			return FileModel::where('userID','=',$userID)->where('cloudID','=',$cloudID)->where('path','=',$path)->where('file_name','=',$fileName)->get()->first();
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*		path : 	Path to the folder 
	*				For eg if path = '/Project/UniCloud'
	*				The function shall return the contents of this folder 
	*	@return value:
	*				an Array of files each containing fileName, last_modified_time, isDirectory and size of the file
	*	@decription : Returns the file(s) of a user at a particular path on the cloud 
	*
	*/
	public static function getFolderContents($userID, $cloudID, $path){
		return FileModel::where('userID','=',$userID)->where('cloudID','=',$cloudID)->where('path','=',$path)
				->select(array('file_name','last_modified_time','is_directory','size'))->get()->toArray();
		//toJson() can also be used in place of toArray 
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*		path : 	Path to the folder 
	*				For eg if path = '/Project/UniCloud'
	*				The function shall return the contents of this folder 
	*	@return value:
	*				an Array of files each containing fileID, file_name, is_directory
	*	@decription : Returns the file(s) of a user at a particular path on the cloud 
	*
	*/
	public static function getFolderContentsPrecise($userID, $cloudID, $path){
		return FileModel::where('userID','=',$userID)->where('cloudID','=',$cloudID)->where('path','=',$path)
						->select(array('fileID','file_name','is_directory','rev'))->get()->toArray();
		//toJson() can also be used in place of toArray 
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		fileID : ID of the file
	*	@return value:
	*	 	None
	*	@decription : Adds a new entry to Temp table
	*				  Temp table is supposed to hold entries of files which are already present on server
	*	
	*/
	public static function addTempEntry($fileID){
		$temp = new Temp();
		$temp->fileID = $fileID;
		$temp->save();
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		fileID : ID of the file
	*	@return value:
	*	 	Boolean : true if temp entry exists otherwise false
	*	@decription : Adds a new entry to Temp table
	*				  Temp table is supposed to hold entries of files which are already present on server
	*	
	*/
	public static function TempFileExists($fileID){
		$file = Temp::where('fileID','=',$fileID)->get()->first();
		if($file ==NULL )return false;
		else return true;
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*	@return value:
	*	 	None
	*	@decription : Resets the file state 
	*				  Deletes all file entries of a cloud 
	*
	*/
	public static function resetFileState($userID, $cloudID){
		FileModel::where('userID','=',$userID)->where('cloudID','=',$cloudID)->delete();
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*	@return value:
	*	 	string: cursor previously returned by the cloud 
	*
	*/
	public static function getOldCursor($userID, $cloudID){
		return UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->get()
																		->first()->pluck('cursor');
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*	@return value:
	*	 	None
	*	@decription : Sets the new cursor 
	*
	*/	
	public static function setNewCursor($userID, $cloudID, $cursor){

		$userCloudInfo= UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)
																				->get()->first();
		$userCloudInfo->cursor = $cursor;	
		$userCloudInfo->save();

	}
/**********************************************************************************************/
}