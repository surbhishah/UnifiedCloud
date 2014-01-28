<?php
class UnifiedCloud {
/**********************************************************************************************/
	/*
	*	@params:
	*		fileName = Name of the file along with extension Eg : file.txt 
	*		userCloudID : ID of the user 's cloud
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
		$file->hash = $fileArray['hash'];
		$file->save();

	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userCloudID : ID of the user 's cloud
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
	*		userCloudID : ID of the user 's cloud
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
	*		userCloudID : ID of the user 's cloud
	*		path : Path to the file For eg /Project/UniCloud for a file at /Project/UniCloud/file.txt
	*		fileName:	Name of the file For eg file.txt
	*	@return value:
	*	 	None
	*	@decription : Deleted the file at user's cloud 
	*
	*/
	public static function deleteFile($userCloudID, $path, $fileName){
		FileModel::where('user_cloudID','=',$userCloudID)->where('path','=',$path)
					->where('file_name','=',$fileName)->delete();
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userCloudID : ID of the user 
	*		path : 	Path to the folder 
	*				For eg if path = '/Project/UniCloud'
	*				The function shall return the contents of this folder 
	*	@return value:
	*				an Array of files each containing fileName, last_modified_time, isDirectory and size of the file
	*	@decription : Returns the file(s) of a user at a particular path on the cloud 
	*
	*/
	public static function getFolderContents($userCloudID, $path){
		return FileModel::where('user_cloudID','=',$userCloudID)->where('path','=',$path)->get()->toArray();
		// I am making this function generic and not specifically getting particular attributes 
		// so that when db caches this query , it will be more effective
		// Also, may functions require FolderContents in different attributes
		// It is in my view better not to make similar functions , just returning
		// different attributes
//				->select(array('file_name','last_modified_time','is_directory','size'))->get()->toArray();
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
	*	@return value:
	*	 	Boolean : Array of all clouds of the user
	*/
		
	public static function getClouds($userID) {
		// user_cloudID is also returned

		$clouds = DB::table('user_cloud_info')
						->join('clouds',function($join) use ($userID){
							$join->on('user_cloud_info.cloudID','=','clouds.cloudID')
								->where('user_cloud_info.userID','=',$userID);
						})->select('clouds.name','user_cloud_info.user_cloudID','user_cloud_info.user_cloud_name')
						->orderBy('clouds.name')
						->get();
		return $clouds;
		//return UserCloudInfo::where('userID','=',$userID)->get();
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		email : email of the user ..email is the email with our app
	*	@return value:
	*	 	Boolean : userID of the user with this email
	*/

	public static function getUserID($email) {
		//user cannot sign in without a valid email id, therefore no checking for
		//validity of email.
		return User::where('email','=',$email)->get()->first()->pluck('userID');
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userID : ID of the user 
	*		userCloudName: name specified by user to name his cloud
	*		uid: unique id returned by dropbox, it uniquely identifies a user of dropbo
	*		cloudID: ID of the cloud
	*		accessToken: accessToken received from the cloud
	*	@return value:
	*	 	Boolean : userCloudID of the new cloud 
	*/

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
	//TODO Surbhi Issue: this function does not add empty directories				 	
		        	$zip->addEmptyDir($folderPath);
				 	foreach ($files as  $file) {
				 		$fileLocation  = $filesDestination.$file['fileID'];
				 		if($file['is_directory']==false && file_exists($fileLocation) == false){
				 			Log::info("File does not exist in UnifiedCloud::createZip",array('file/folder'=>$file['file_name'] , 'fileLocation'=>$fileLocation));
				 			throw new Exception('File does not exist');		
				 		}
				 		$zip->addFile($fileLocation, $folderPath.'/'.$file['file_name']);
				 	}
				 	//Log::info("Adding to zip ",array('folderPath',$folderPath));
				 	
				 }	
				$zip->close();
				return $zipFileName;
		}	
/**********************************************************************************************/
	/*
	*	@params:
	*		uid: unique id returned by dropbox, it uniquely identifies a user of dropbo
	*		cloudID: ID of the cloud
	*	@return value:
	*	 	Boolean : true if user with same uid and cloudID exists
	*				  false if user with same uid and cloudID does not exist
	*/
		public static function userAlreadyExists($uid, $cloudID){
			$userCloudInfo= UserCloudInfo::where('uid','=',$uid)->where('cloudID','=',$cloudID)->get()->first();
			if($userCloudInfo==null)return false;
			else return true;
		}
/**********************************************************************************************/
	/*
	*	@params:
	*		userID: userID of the user
	*		cloudID: ID of the cloud
	*		userCloudName: Name assigned by user to his cloud
	*	@return value:
	*	 	Boolean : true if user already has cloud with that name and cloudProvider
	*					For eg: a user may have two clouds named "ABC" with dropbox and google Drive
	*					This is not a problem but he must not have two clouds named "ABC" on dropbox
	*				  false otherwise
	*/
		public static function userCloudNameAlreadyExists($userID,$cloudID, $userCloudName){
			$userCloudInfo = UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)
							->where('user_cloud_name','=',$userCloudName)->get()->first();
			if($userCloudInfo == null)return false;
			else return true;
		}
/**********************************************************************************************/
	/*
	*	@params:
	*		userCloudID: ID of the user's cloud
	*		fullPath: The path to the folder or file whose hash is required
	*					In dropbox, files do not have hash
	*	@return value:
	*	 	Boolean : hash of the file/folder 
	*/
		public static function getHash($userCloudID, $fullPath){
			list($path, $fileName)= Utility::splitPath($fullPath);
			$file= FileModel::where('user_cloudID','=',$userCloudID)->where('path','=',$path)
							->where('file_name','=',$fileName)->get()->first();			
			if($file==null)return null;
			else return $file->hash;
		}
/**********************************************************************************************/
}
