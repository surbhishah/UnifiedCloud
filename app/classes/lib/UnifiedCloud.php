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
	public static function addOrUpdateFile($userCloudID, $fileArray){
		$file = UnifiedCloud::getFile($userCloudID, $fileArray['path'], $fileArray['fileName']);
		if($file == null){
			$file = new FileModel();
		}
		$file->user_cloudID = $userCloudID;
		$file->path = $fileArray['path'];
		$file->file_name = $fileArray['fileName'];
		$file->last_modified_time= Utility::changeDateFormatToDBFormat($fileArray['lastModifiedTime']);
		$file->is_directory= $fileArray['isDirectory'];
		$file->rev = $fileArray['rev'];
		$file->size = $fileArray['size'];
		$file->save();

	}
	/*
	*	@params:
	*		userID : ID of the user 
	*		cloudID: ID of the cloud
	*	@return value:
	*	 	string: access_token
	*	@decription : Returns access Token of a user 
	*
	*/
	public static function getAccessToken($userCloudID){
		return UserCloudInfo::where('user_cloudID','=',$userCloudID)->get()
									->first()->pluck('access_token');		
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
	public static function getFile($userCloudID, $path, $fileName){
			return FileModel::where('user_cloudID','=',$userCloudID)
							->where('path','=',$path)->where('file_name','=',$fileName)
							->get()->first();
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
	public static function getFolderContents($userCloudID, $path){
		return FileModel::where('user_cloudID','=',$userCloudID)->where('path','=',$path)
				->select(array('file_name','last_modified_time','is_directory','size'))->get()->toJson();
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
		$cursor =  UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->get()
																		->first()->pluck('cursor');
		if(is_null($cursor))return null;
		else return $cursor;																		
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
	
	
	public static function getClouds($userID) {
		// user_cloudID is also returned 
		return UserCloudInfo::where('userID','=',$userID)->get()->toArray();
	}

	public static function getUserID($email) {
		//user cannot sign in without a valid email id, therefore no checking for
		//validity of email.
		return User::where('email','=',$email)->get()->first()->pluck('userID');
	}

	public static function setAccessToken($userID,$userCloudName,$uid,$cloudID,$accessToken) {
		$userCloudInfo = new UserCloudInfo;
		$userCloudInfo->userID = $userID;
		$userCloudInfo->user_cloud_name=$userCloudName;
		$userCloudInfo->uid  = $uid;
		$userCloudInfo->cloudID = $cloudID;
		$userCloudInfo->access_token = $accessToken;
		$userCloudInfo->save();
		return $userCloudInfo->user_cloudID;
	}
	public static function setHasUserFiles($userID,$cloudID,$value) {
		$userCloudInfo = UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->get()->first();
		$userCloudInfo->has_user_files = $value;
		$userCloudInfo->save();
	}

	public static function getHasUserFiles($userID,$cloudID) {
		//Log::info('parameter passed: ',array('user' => $userID,'cloud' => $cloudID));
		//Log::info('Query result: ',array('result' => UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->get()->first()->pluck('has_user_files')));
		return UserCloudInfo::where('userID','=',$userID)
			->where('cloudID','=',$cloudID)
			->pluck('has_user_files');
	}
/**********************************************************************************************/

	/*
	*	@params:
	*		jsonFilePath : path to the json file created by downloadFolder function
	*						This json must be of the form folder=>files 

	*					
	*		cloudName : Name of the cloud ..but note that this name will be used to access that folder
	*					under public/temp so the name is not case insensitive 
	*					Make a static private constant in the respective cloud class and pass it
	*	@return value:
	*	 	Returns the name of the new zip file created . This file should be sent to user and then deleted
	*	@decription : Creates a zip file with all subfolders and files and returns it 
	*
	*/	
	// Pass static constant of cloud class as cloudName ONLY
		public static function createZip($jsonFilePath, $cloudName){
				if(!file_exists($jsonFilePath)){
					throw new Exception("Json file not found in createZip function ");
				}	
				// Path where temp files have been stored
				$filesDestination = public_path().'/temp/'.$cloudName.'/downloads/';
				
				//Get file from json
				$fileJson= File::get($jsonFilePath);
				
				// Map json to array
				$fileArray=json_decode($fileJson, true);// True for associative array 
				
				// We assume that first element is the main folder to be zipped 
				list($folderPath, $files )= each($fileArray);
				list($path, $folderName)= Utility::splitPath($folderPath);
				
				// Zip directory
				$zipFileName = uniqid().'___'.$folderName.'.zip';
			

				// Removing extraneous path . Keep path starting from the folder to be downloaded 
				$pathLength = strlen($path);
				foreach ($fileArray as $folderPath => $files) {
					$newFolderPath = substr($folderPath, $pathLength);			
					$newFileArray[$newFolderPath]= $files;
				}
			
				// Create a zip			
				$zip = new ZipArchive;
				if(!$zip->open($zipFileName, ZipArchive::CREATE)){
						Log::error("Zipped file could not be opened ");
						throw new Exception('Zipped file could not be opened in UnifiedCloud::createZip');
				}
			
				// Add files and folders to zip 
		        foreach($newFileArray as $folderPath => $files){
				 	Log::info("Adding to zip ",array('folderPath',$folderPath));
				 	foreach ($files as  $file) {
				 		$fileLocation  = $filesDestination.$file['fileID'];
				 		if($file['is_directory']==false && file_exists($fileLocation) == false){
				 			Log::info("File does not exist in UnifiedCloud::createZip",array('file/folder'=>$file['file_name'] , 'fileLocation'=>$fileLocation));
				 			throw new Exception('File does not exist');		
				 		}
				 		$zip->addFile($fileLocation, $folderPath.'/'.$file['file_name']);
				 	}
				 	$zip->addEmptyDir($folderPath);
				 }	
				$zip->close();
				return $zipFileName;
		}	
/**********************************************************************************************/
		public static function userAlreadyExists($uid, $cloudID){
			$userCloudInfo= UserCloudInfo::where('uid','=',$uid)->where('cloudID','=',$cloudID)->get()->first();
			if($userCloudInfo==null)return false;
			else return true;
		}
		public static function userCloudNameAlreadyExists($userID,$cloudID, $userCloudName){
			$userCloudInfo = UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)
							->where('user_cloud_name','=',$userCloudName)->get()->first();
			if($userCloudInfo == null)return false;
			else return true;
		}
		public static function getHash($userCloudID, $fullPath){
			list($path, $fileName)= Utility::splitPath($fullPath);
			$file= FileModel::where('user_cloudID','=',$userCloudID)->where('path','=',$path)
							->where('file_name','=',$fileName)->get()->first();			
			if($file==null)return null;
			else return $file->hash;
		}
}









