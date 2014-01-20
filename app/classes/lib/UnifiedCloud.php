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
	
	
	public static function getCloudsByEmail($email) {
		$userID = UnifiedCloud::getUserId($email);
		// return DB::table('user_cloud_info')
		// ->join('clouds',function($join){
		// 	$join->on('user_cloud_info.cloudID','=','clouds.cloudID')
		// 		 ->where('user_cloud_info.userID','=',UnifiedCloud::getUserId($email));
		// })
		// ->select('clouds.cloudID','clouds.name');
		return DB::table('user_cloud_info')
			->join('clouds','clouds.cloudID','=','user_cloud_info.cloudID')
			->select('clouds.cloudID','clouds.name')
			->where('userID','=',$userID)
			->get();
	}

	public static function getUserId($email) {
		//user cannot sign in without a valid email id, therefore no checking for
		//validity of email.
		return DB::table('users')->where('email',$email)->pluck('userID');
	}

	public static function setAccessToken($email,$cloudID,$accessToken) {
		$userID = UnifiedCloud::getUserId($email);
		DB::table('user_cloud_info')->insert(
				array('userID' => $userID, 'cloudID' => $cloudID, 'access_token' => $accessToken)
			);
	}

	public static function setHasUserFiles($userID,$cloudID,$value) {
		$userCloudInfo = UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->get()->first();
		$userCloudInfo->has_user_files = $value;
		$userCloudInfo->save();
	}

	public static function getHasUserFiles($userID,$cloudID) {
		Log::info('parameter passed: ',array('user' => $userID,'cloud' => $cloudID));
		Log::info('Query result: ',array('result' => UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)->get()->first()->pluck('has_user_files')));
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
					throw new Exception('Zipped file could not be opened');
			}
		
			// Add files and folders to zip 
	        foreach($newFileArray as $folderPath => $files){
			 	Log::info("Adding to zip ",array('folderPath',$folderPath));
			 	foreach ($files as  $file) {
			 		$fileLocation  = $filesDestination.$file['fileID'];
			 		if($file['is_directory']==false && file_exists($fileLocation) == false){
			 			Log::info("File does not exist",array('file/folder'=>$file['file_name'] , 'fileLocation'=>$fileLocation));
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

}