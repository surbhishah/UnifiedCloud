
<?php
// TODO CHNAGE TIME FORMAT OF DATA RETURNED BY Dropbox
class Dropbox implements CloudInterface{
	private static $clientIdentifier = "Project-Kumo";
	private static $cloudID = '1';	

/************************************************************************************************/
	/*
	 *	@params : 
	 *	'userID'				: ID of the user 
	 *	'userfile' 				: File uploaded by the user
	 *	'cloudDestinationPath': The path where the file is to be uploaded 	
	 *	For eg : if cloudDestinationPath =  '/Project/Files' then file will be uploaded at '/Project/Files/[filename]'
	 *			if cloudDestinationPath='/' then file will be uploaded at '/[filename]'
	 *	@return value : if file upload is successful then this method returns Metadata of that file
	 *	@exceptions: Exception	
	*/
	public function upload($userID, $userfile, $cloudDestinationPath){
			$result = null;
			try{
				// Set the path to the directory where the temp files will be stored
				// We append the userID of the user so that files of same name do not clash with each other
				$serverDestinationPath = public_path().'/temp/dropbox/uploads/';
				if(!is_dir($serverDestinationPath.$userID)){
					mkdir($serverDestinationPath.$userID);
				}

				$serverDestinationPath = public_path().'/temp/dropbox/uploads/'.$userID.'/';
				
				// Get the file from the form
				$file = $userfile;

				// Get the name of the file of the user
				$fileName = $file->getClientOriginalName();

				// Store the file on the server
				$file->move($serverDestinationPath, $fileName);

				// Open the "stream" of the file so that it can be uploaded on the cloud
				$fileStream = fopen($serverDestinationPath.$fileName, 'rb');

				// Get client object 
				$client = $this->getClient($userID);

				// Upload file using client , This method returns the metadata of the file uploaded
				if($cloudDestinationPath=='/'){
					$cloudDestinationFullPath = $cloudDestinationPath.$fileName;
				}
				else{
					$cloudDestinationFullPath = $cloudDestinationPath.'/'.$fileName;

				}
				$result = $client->uploadFile($cloudDestinationFullPath, Dropbox\WriteMode::add(), $fileStream);

				// Update UnifiedCloud database that a new file has been uploaded 
//				UnifiedCloud::addFileInfo($fileName, $userID,self::$cloudID, $cloudDestinationPath, 
//					$result['is_dir'],$result['modified'],$result['size'],$result['rev']);
			

				// DO NOT UPDATE THIS may create inconsistencies
				// Rather, bring in delta from dropbox so that 
				// there is no chance of an inconsistency
				// CALL refresh Folder or something like that	

				// Delete the file on the server, Deleting the files because it is highly unlikely that 	
				// user will download a file he has uploaded right now 
				chmod($serverDestinationPath.$fileName, 0750);
				File::delete($serverDestinationPath.$fileName);

				Utility::removeDir($serverDestinationPath);

			
			}catch(Exception $e){
				throw new Exception($e->getMessage());
			}
			// If upload failed $result will have null 
			// If upload was successful $result will have Metadata of the file 
			return $result;

		}
/************************************************************************************************/

	/*
	 *	@params : must contain 'userID', 'fileName' and 'cloudSourcePath'
	 *	'userID'				: ID of the user 
	 *	'fileName' 				: Name of the file
	 *	'cloudSourcePath': The path from where file is to be downloaded
	 *	
	 *	For eg : if cloudSourcePath =  '/Project/Files' then file will be downloaded from '/Project/Files/[filename]'
	 *	@return value: fileDestination : Path on server where file is saved  
	 *	@exceptions: Exception
	*/
	public function download($userID, $cloudSourcePath, $fileName){
	
		$serverDestinationPath = public_path().'/temp/dropbox/downloads/';
		
		 try{
			if($cloudSourcePath=='/'){
				$cloudFullSourcePath = $cloudSourcePath.$fileName;
			}
			else{
				$cloudFullSourcePath = $cloudSourcePath.'/'.$fileName;

			}
			// Get client object
			$client = self::getClient($userID);
			// Get fileID of this file from the database
			$file = UnifiedCloud::getFile($userID, self::$cloudID, $cloudSourcePath, $fileName );
			if($file==null){
				throw new Exception("File does not exist");
			}
			$fileID = $file->fileID;										//UNCOMMENT THIS LATER
//			$fileID='1';													//COMMENT THIS LATER
			

			// Check if file with this fileID is already present on the server, if yes DO NOT download
			// However, we also need to check if the file is up-to-date
			// fileDestination = destination of file on our server 
			$fileDestination= $serverDestinationPath.$fileID;
// SEE THIS ONCE AGAIN
			if(UnifiedCloud::TempFileExists($fileID)	){
				// Check if the file is up to date , so get the metadata from dropbox
				$fileMetaData = $client->getMetaData($cloudFullSourcePath);
				// Check if the rev values are same , if they are then send this file , DO NOT download
				if($fileMetaData['rev']==$file->rev){ //otherwise download the file
						return $fileDestination;
					}
			}// otherwise download the file 
					
	
			// Open the "stream" of the file so that file coming from dropbox can be saved to it
			$fileStream=fopen($fileDestination, "wb");
	
			// Download file from Dropbox
			$result=$client->getFile($cloudFullSourcePath.'/'.$fileName, $fileStream);	
			
			// Update the database table temp that we have stored this file on server
			UnifiedCloud::addTempEntry($fileID);	
			// return the destination on the server where the file is saved
			return $fileDestination;

		}catch(Exception $e){
			throw new Exception($e->getMessage());
			//return null;
		}

	}
/************************************************************************************************/

	/*
	 *	@params : 
	 *	'userID'				: ID of the user 
	 *	@return value: instance of Dropbox/Client class
	 *	@exceptions: AccessTokenNotFoundException
	*/
	private function getClient($userID){
		// We can save email ID of the user in session or 
		// we can save the user ID directly
		// This will prevent one query on the database 
		// because ultimately we always need the userID of the user
		// Security concerns: In case attacker gets hold of userID from the session
		// For now, I am hard coding the user ID and have also hard coded the access token in db
			$userID = '1';													// COMMENT THIS LATER
			//$user = User::find($userID); 									// UNCOMMENT THIS LATER
			// $path = app_path().'/accessToken.txt';			// TESTING
			// File::append($path,'Hello Surbhi you are awesome');
			$accessToken = UnifiedCloud::getAccessToken($userID, self::$cloudID );
			if($accessToken == null){
				error_log('Access token is null');
				throw new AccessTokenNotFoundException();
				return null;
			}else{
			//	File::append($path,$accessToken);		//TESTING
				$client = new Dropbox\Client($accessToken,self::$clientIdentifier);
				return $client;
			}
	}
/************************************************************************************************/


	/*
	*	@params:
	*	userID = ID of the user 
	* 	folderPath = Path of the folder whose contents have been sought
	*	For eg : if folderPath = /Projects/Unicloud then contents of Unicloud will be returned
	*	@return value: Meta data of the folder and its files and folders
	* 	@Exceptions:	
	*/
	public function getFolderContents($userID, $folderPath){
		// Get client object 
		try{
			$client = self::getClient($userID);
			// Dropbox API requires that there should be no trailing slash except if it is root '/'
			// Obtain fileMetaData from Dropbox
			$fileMetaData=$client->getMetadataWithChildren($folderPath);
			return $fileMetaData;

		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	userID = ID of the user 
	* 	folderPath = Path of the folder to be created 
	*	For eg : if folderPath = /Projects/Unicloud then Unicloud folder will be created 
	*	@return value: Meta data of the new folder 
	* 	@Exceptions:	Exception
	*/
	public function createFolder($userID, $folderPath){
		try{
			// We cannot create root directory 
			if($folderPath == '/'){
				throw new Exception('Invalid folder path passed to createFolder function in Dropbox.php');
			}
			// Remove the trailing /
			$client = self::getClient($userID);
			return $client->createFolder($folderPath);
		
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	userID = ID of the user 
	* 	path = Path of the file/folder to be deleted 
	*	For eg : if folderPath = /Projects/Unicloud then Unicloud folder will be deleted  
	*	@return value: Meta data of the new folder 
	* 	@Exceptions:	Exception
	*/
	public function delete($userID , $path){
		try{
			$client = self::getClient($userID);
			if($path == '/'){	
				throw new Exception('Invalid folder path passed to delete function in Dropbox.php');
			}			
			$result =$client->delete($path);
			return $result;

		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}

	}
/************************************************************************************************/
// To be called when a user adds a new cloud to UnifiedCloud
// At that time , we bring in all information from dropbox and save it to our database
// After this we just bring the delta (ie the changes that have been made) and reflect them in our database

	public function getFullFileStructure($userID){
		 try{
			$client = self::getClient($userID);
			// First null : cursor
			// Second null: path_prefix
			$data = $client->getDelta(null,null);
			$hasMore = $data['has_more'];
			
			//cursor :A string that encodes the latest information that has been returned. 
			//On the next call to /delta, pass in this value.
			$cursor = $data['cursor'];
			UnifiedCloud::setNewCursor($userID, self::$cloudID, $cursor);
			$fileData = $data['entries'];
			//reset is always true on the initial call to /delta (i.e. when no cursor is passed in). 
			//$reset = $data['reset'];
			foreach ($fileData as $file) {
				$completePath = $file[1]['path'];
				list($path, $fileName)=	Utility::splitPath($completePath);
				UnifiedCloud::addFileInfo($fileName, $userID, self::$cloudID,$path,$file[1]['is_dir'],$file[1]['modified'],$file[1]['size'],$file[1]['rev']);
			}
			return $data;
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}
/************************************************************************************************/
	public function refreshFolder($userID, $folderPath){

	}
/************************************************************************************************/
	public function refreshFullFileStructure($userID){
		try{
			$client = self::getClient($userID);
			$oldCursor = UnifiedCloud::getOldCursor($userID, self::$cloudID);
 			//Cursor  : A string that is used to keep track of your current state. 
			//On the next call pass in this value to return delta entries 
			//that have been recorded since the cursor was returned.
			// if oldCursor = null , directly null will be passed to function call delta
			// everything remains the same

			// $path = app_path().'/accessToken.txt';
			// File::put($path, $oldCursor);			// TESTING
			

			// First parameter  : cursor
			// Second null: path_prefix
			// if path_prefix= null, full file structure info is returned 
			$data = $client->getDelta($oldCursor,null);
			$hasMore = $data['has_more'];
			$cursor = $data['cursor'];
			UnifiedCloud::setNewCursor($userID, self::$cloudID, $cursor);
			$fileData = $data['entries'];
			$reset = $data['reset'];
			if($reset == true){
				 //If true, clear your local state before processing the delta entries. 
				 //reset is always true on the initial call to /delta (i.e. when no cursor is passed in). 
				 //Otherwise, it is true in rare situations, such as after server or account maintenance, 
				 //or if a user deletes their app folder
				UnifiedCloud::resetFileState($userID, self::$cloudID);
			}
			
			foreach ($fileData as $file) {
				$completePath = $file[1]['path'];
				list($path, $fileName)=	Utility::splitPath($completePath);
				$file_db = UnifiedCloud::getFile($userID, self::$cloudID, $path, $fileName);
				if($file_db == null){// Does this file exist?
					// If no then, create such a file 
					UnifiedCloud::addFileInfo($fileName, $userID, self::$cloudID,$path,$file[1]['is_dir'],$file[1]['modified'],$file[1]['size'],$file[1]['rev']);
				}
				else{
					// If yes ...ie the file exists but may have changed
					// Delete the old entry and create a new entry
					$file_db->delete();
					UnifiedCloud::addFileInfo($fileName, $userID, self::$cloudID,$path,
							$file[1]['is_dir'],$file[1]['modified'],$file[1]['size'],$file[1]['rev']);
				}

			}
			return $data;

		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}		
	}
/************************************************************************************************/











	
}