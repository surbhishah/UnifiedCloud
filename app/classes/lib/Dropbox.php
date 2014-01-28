<?php
class Dropbox implements CloudInterface{
	private static $clientIdentifier = "Project-Kumo";
	private static $cloudID = '1';
	private static $cloudName = 'dropbox';// Do not change used by DownloadFolder
/************************************************************************************************/
	/*
	 *	@params :
	 *	'userCloudID'			: ID of the user's cloud
	 *	'userfile' 				: File uploaded by the user
	 *	'cloudDestinationPath': The path where the file is to be uploaded
	 *	For eg : if cloudDestinationPath =  '/Project/Files' then file will be uploaded at '/Project/Files/[filename]'
	 *			if cloudDestinationPath='/' then file will be uploaded at '/[filename]'
	 *	@return value : if file upload is successful then this method returns Metadata of that file
	 *	@exceptions: Exception
	*/
	public function upload($userCloudID, $userfile, $cloudDestinationPath){
			$result = null;
			try{
				// Set the path to the directory where the temp files will be stored
				// We append the userCloudID of the user so that files of same name do not clash with each other
				$serverDestinationPath = public_path().'/temp/dropbox/uploads/';
				if(!is_dir($serverDestinationPath.$userCloudID)){
					mkdir($serverDestinationPath.$userCloudID);
				}

				$serverDestinationPath = public_path().'/temp/dropbox/uploads/'.$userCloudID.'/';

				// Get the file from the form
				$file = $userfile;

				// Get the name of the file of the user
				$fileName = $file->getClientOriginalName();

				// Store the file on the server
				$file->move($serverDestinationPath, $fileName);

				// Open the "stream" of the file so that it can be uploaded on the cloud
				$fileStream = fopen($serverDestinationPath.$fileName, 'rb');

				// Get client object
				$client = $this->getClient($userCloudID);

				// Upload file using client , This method returns the metadata of the file uploaded
				if($cloudDestinationPath=='/'){
					$cloudDestinationFullPath = $cloudDestinationPath.$fileName;
				}
				else{
					$cloudDestinationFullPath = $cloudDestinationPath.'/'.$fileName;

				}
				$result = $client->uploadFile($cloudDestinationFullPath, Dropbox\WriteMode::add(), $fileStream);
				
				/* ===============Code added by Abhishek ============== */
				$newFile = array();
				$newFile['path']=$cloudDestinationPath;
				$newFile['fileName']=$fileName;
				$newFile['lastModifiedTime']=$result['modified'];
				$newFile['rev']=$result['rev'];
				$newFile['size']=$result['size'];
				$newFile['isDirectory']=$result['is_dir'];
				$newFile['hash']=null;// Passing null because we dont have hash values for these 
				// but we might get them in the future if $file is actually a folder 
				FileModel::addOrUpdateFile($userCloudID, $newFile);

				/* ======================================================= */

				// refreshing our database with updates from dropbox
				
				//ABHISHEK:: code commented because refresh is no more needed after an upload.
				//but won't folder data be changed because folder data. 
				$this->refreshFolder($userCloudID, $cloudDestinationPath);
				
				// Update app database that a new file has been uploaded


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
				Log::info("Exception raised in Dropbox::upload");
				Log::error($e);
				throw $e;
			}
			// If upload failed $result will have null
			// If upload was successful $result will have Metadata of the file
			// NOT REQUIRED 
			// MAY BE COMMENTED LATER ABHISHEK
			return $result;

		}
/************************************************************************************************/

	/*
	 *	@params : must contain 'userCloudID', 'fileName' and 'cloudSourcePath'
	 *	'userCLoudID'			: ID of the user's cloud
	 *	'fileName' 				: Name of the file
	 *	'cloudSourcePath': The path from where file is to be downloaded
	 *
	 *	For eg : if cloudSourcePath =  '/Project/Files' then file will be downloaded from '/Project/Files/[filename]'
	 *	@return value: fileDestination : Path on server where file is saved
	 *	@exceptions: Exception
	*/
	public function download($userCloudID, $cloudSourcePath, $fileName){

		$serverDestinationPath = public_path().'/temp/dropbox/downloads/';

		 try{
			if($cloudSourcePath=='/'){
				$cloudFullSourcePath = $cloudSourcePath.$fileName;
			}
			else{
				$cloudFullSourcePath = $cloudSourcePath.'/'.$fileName;

			}
			// Get client object
			$client = $this->getClient($userCloudID);
			// Get fileID of this file from the database
			$file = FileModel::getFileAttributes($userCloudID, $cloudSourcePath, $fileName ,array('fileID','rev'));
			if($file==null){
				throw new Exception("File does not exist");
			}
			$fileID = $file->fileID;									
			// Check if file with this fileID is already present on the server, if yes DO NOT download
			// However, we also need to check if the file is up-to-date
			// fileDestination = destination of file on our server
			$fileDestination= $serverDestinationPath.$fileID;
			if(Temp::TempFileExists($fileID)	){
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
			Temp::addTempEntry($fileID);
			// return the destination on the server where the file is saved
			return $fileDestination;

		}catch(Exception $e){
				Log::info("Exception raised in Dropbox::download",
					array('userCloudID'=>$userCloudID, 'cloudSourcePath'=>$cloudSourcePath, 'fileName'=>$fileName));
				Log::error($e);
				throw $e;
		}


	}
/************************************************************************************************/
	/*
	 *	@params :
	 *	'userCloudID'				: ID of the user
	 *	@return value: instance of Dropbox/Client class
	 *	@exceptions: AccessTokenNotFoundException
	*/
	private function getClient($userCloudID){
			try{
				$accessToken = UserCloudInfo::getAccessToken($userCloudID);
				if($accessToken == null){
					throw new AccessTokenNotFoundException();
					return null;
				}else{
					$client = new Dropbox\Client($accessToken,self::$clientIdentifier);
					return $client;
				}
			}catch(Exception $e){
				Log::info("Exception raised in Dropbox::getClient",array('userCloudID'=>$userCloudID));
				Log::error($e);
				throw $e;
			}

	}
/************************************************************************************************/
	/*
	*	@params:
	*	userCloudID = ID of the user's cloud
	* 	folderPath = Path of the folder whose contents have been sought
	*	cached: if cached= true, data will be sent from cache is it is present
	*			otherwise it will be sent from database
	*	For eg : if folderPath = /Projects/Unicloud then contents of Unicloud will be returned
	*	@return value: Meta data of the folder and its files and folders
	*	@description : This function gets new meta data from dropbox ie refreshes 
	*					our database and returns new data
	* 	@Exceptions:	Exception
	*/
	public function getFolderContents($userCloudID, $folderPath, $cached='false'){
		try{
			 $key = $userCloudID.$folderPath;
			 if(Cache::has($key) && $cached =='true'){ //cached is a string, not boolean
			 	return Cache::get($key);
			 }
			 else{
			 	$this->refreshFolder($userCloudID, $folderPath);
			 	$folderContents= FileModel::getFolderContents($userCloudID, $folderPath);
			 	Cache::put($key, $folderContents, 10);
			 	return $folderContents;
			 }
		}catch(Exception $e){
			Log::info("Exception raised in Dropbox::refreshFolder",array('userCloudID'=>$userCloudID, 'folderPath'=>$folderPath));
			Log::error($e);				
			throw $e;
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	userCloudID = ID of the user
	* 	folderPath = Path of the folder whose contents have been sought
	*	For eg : if folderPath = /Projects/Unicloud then contents of Unicloud will be returned
	*	@return value: Meta data of the folder and its files and folders
	* 	@Exceptions:	Exception
	*/
	private function refreshFolder($userCloudID, $folderPath){

		try{
			$client = self::getClient($userCloudID);
			// Dropbox API requires that there should be no trailing slash except if it is root '/'
			// Obtain fileMetaData from Dropbox
			$hash = FileModel::getHash($userCloudID, $folderPath);
			if($hash == null){
				$newMetaData = $client->getMetadataWithChildren($folderPath);
				$receivedData = true;
			}
			else{
				list($receivedData, $newMetaData) = $client->getMetadataWithChildrenIfChanged($folderPath, $hash);
			}
			// if data is received , then update our database and then return the folder contents
			if($receivedData == true){
				$folderData = array();// This is the folder for which data has been received
				if($folderPath=='/'){// Dropbox does not return these attributes for root 
					$folderData['path']='root';
					$folderData['fileName']='/';
					$folderData['lastModifiedTime']= null;// Does not matter ..never needed ..null sets current time in db
					$folderData['rev']='rev';// rev of root is not known and rev is not nullable
				}
				else{
					list($path, $folderName)= Utility::splitPath($newMetaData['path']);
					$folderData['path']=$path;
					$folderData['fileName']=$folderName;
					$folderData['lastModifiedTime']=$newMetaData['modified']; 
					$folderData['rev']=$newMetaData['rev'];
				}
				$folderData['hash'] = $newMetaData['hash'];
				$folderData['isDirectory']=true;
				$folderData['size']=$newMetaData['size'];
				// $filesArray =  Array of files in our db
				$filesArray= FileModel::getFolderContents($userCloudID, $folderPath);
				$completePaths= array();
				$i=0;
				foreach ($filesArray as $file) {
					$completePaths[$i]= Utility::joinPath($file['path'],$file['file_name'] );
					$i++;
				}
				//$filesReceived = Array of files from dropbox
				$filesReceived = $newMetaData['contents'];
				$i=0;
				$completePathsReceived = array();
				foreach ($filesReceived as $file) {
					$completePathsReceived[$i]= $file['path'];
				}
				// $deletedFilesPaths =  paths to files which are present in our database($filesArray) 
				//	but have been deleted at dropbox ($filesReceived)
				$deletedFilesPaths = array_diff($completePaths, $completePathsReceived);
				// Delete outdated entries from our db
				foreach ($deletedFilesPaths as $completePath) {
					list($path, $fileName) =  Utility::splitPath($completePath);
					FileModel::deleteFile($userCloudID, $path, $fileName);
				}
				// Update the folder data specially hash 
				FileModel::addOrUpdateFile($userCloudID, $folderData);
				foreach($filesReceived as $file){// Each file may be a folder or a file
					list($path, $fileName)= Utility::splitPath($file['path']);
					$newFile['path']=$path;
					$newFile['fileName']=$fileName;
					$newFile['lastModifiedTime']=$file['modified'];
					$newFile['rev']=$file['rev'];
					$newFile['size']=$file['size'];
					$newFile['isDirectory']=$file['is_dir'];
					$newFile['hash']=null;// Passing null because we dont have hash values for these 
					// but we might get them in the future if $file is actually a folder 
					FileModel::addOrUpdateFile($userCloudID, $newFile);
				}
			}
			// if no data is received ..means data we have is correct, send it directly
			// TO BE COMMENTED LATER THIS FUNCTION DOES NOT RETURN ANYTHING
			//return $newMetaData;
			//return FileModel::getFolderContents($userCloudID, $folderPath);

		}catch(Exception $e){
				Log::info("Exception raised in Dropbox::refreshFolder",array('userCloudID'=>$userCloudID, 'folderPath'=>$folderPath));
				Log::error($e);				
				throw $e;
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	userCloudID = ID of the user's cloud
	* 	folderPath = Path of the folder to be created
	*	For eg : if folderPath = /Projects/Unicloud then Unicloud folder will be created
	*	@return value: Meta data of the new folder
	* 	@Exceptions:	Exception
	*/
	public function createFolder($userCloudID, $folderPath){
		try{
			// We cannot create root directory
			if($folderPath == '/'){
				throw new Exception('Invalid folder path passed to createFolder function in Dropbox.php');
			}
			// Remove the trailing /
			$client = self::getClient($userCloudID);
			$result =  $client->createFolder($folderPath);
			$this->refreshFolder($userCloudID, $folderPath);
			return $result;

		}catch(Exception $e){
				Log::info("Exception raised in Dropbox::createFolder",array('userCloudID'=>$userCloudID,'folderPath'=>$folderPath));
				Log::error($e);
				throw $e;
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	userCloudID = ID of the user 's cloud
	* 	completepath = Path of the file/folder to be deleted
	*	For eg : if folderPath = /Projects/Unicloud then Unicloud folder will be deleted
	*	@return value: Meta data of the new folder
	* 	@Exceptions:	Exception
	*/
	public function delete($userCloudID , $completePath){
		try{
			$client = self::getClient($userCloudID);
			if($completePath == '/'){
				throw new Exception('Invalid folder path passed to delete function in Dropbox.php');
			}
			$result =$client->delete($completePath);
			list($path, $fileName) = Utility::splitPath($completePath);
			$this->refreshFolder($userCloudID, $path);
			return $result;

		}catch(Exception $e){
				Log::info("Exception raised in Dropbox::delete",array('userCloudID'=>$userCloudID, 'path'=>$completePath));
				Log::error($e);
				throw $e;
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	None
	*	@return value: This function redirects the user to the authentication page of dropbox
	* 	@Exceptions:	Exception
	*/
	public function getRegistrationPage($userCloudName='dropbox'){
        // Redirect user to app authentication page of dropbox
        // uri of authentication is to be obtained using object of WebAuth class
        try{
	        $webauth =$this->getWebAuth(); 
	        $authorizeUrl = $webauth->start($userCloudName);
	        return Redirect::to($authorizeUrl);
		}catch(Exception $e){
				Log::info("Exception raised in Dropbox::getRegistrationPage");
				Log::error($e);
				throw $e;
		}
    }

/************************************************************************************************/
	/*
	*	@params: None
	*	None
	*	@return value: This function returns an object of Dropbox\WebAuth class
	* 	@Exceptions:	Exception
	*/
  // TODO  Redirect URI
    private function getWebAuth(){
    	try{
	        session_start();
	        $path= app_path().'/database/dropbox-app-info.json';
	        $appInfo = Dropbox\AppInfo::loadFromJsonFile($path);
	        $redirectUri = "http://localhost/UnifiedCloud/public/auth/dropbox";// This needs a Https link ..only localhost 
	                                                                    //is allowed for http
	        $csrfTokenStore = new Dropbox\ArrayEntryStore($_SESSION, 'date(format)ropbox-auth-csrf-token');
	        return new Dropbox\WebAuth($appInfo, self::$clientIdentifier, $redirectUri, $csrfTokenStore);
    	
    	}catch(Exception $e){
				Log::info("Exception raised in Dropbox::getWebAuth");
				Log::error($e);
				throw $e;
		}
    }

/************************************************************************************************/
	/*
	*	@params: GET Parameters sent by dropbox
	*	None
	*	@return value: 	This function returns an object of Dropbox\WebAuth class
	* 	@Exceptions:	Exception
	*	@description: 	This function takes in GET parameters returned by dropbox and
	*					Sets accessToken of the user
	*/
    public function getCompletion(){
        try {

            // Get access token of the user now he has authenticated our app
            //$_GET is an array of variables passed to the current script via the URL parameters.
                list($accessToken, $uid, $urlState) = $this->getWebAuth()->finish($_GET);
                $userCloudName = $urlState;
                $userID = Session::get('userID');
                if(User::userAlreadyExists($uid, self::$cloudID)){
					return View::make('complete')
							->with('message','You already have an account with us!');
				}
				else if(UserCloudInfo::userCloudNameAlreadyExists($userID,self::$cloudID, $userCloudName)){
					return View::make('complete')
							->with('message','You already have an account with this name "'.$userCloudName .'" Please choose another fab name!');		
				}
				else{
					$userCloudID = UserCloudInfo::setAccessToken($userID,$userCloudName, $uid, self::$cloudID, $accessToken);
					return Redirect::route('dashboard');
				}
            }
            catch (Dropbox\WebAuthException_BadRequest $ex) {
               Log::info("Dropbox\WebAuthException_BadRequest raised in Dropbox::getCompletion");
               Log::error($ex);
               throw $ex;
               // Respond with an HTTP 400 and display error page...
            }
            catch (Dropbox\WebAuthException_BadState $ex) {
               // Auth session expired.  Restart the auth process.
               header('Location: /dropbox-auth-start');
            }
            catch (Dropbox\WebAuthException_Csrf $ex) {
               Log::info("Dropbox\WebAuthException_Csrf raised in Dropbox::getCompletion");
               Log::error($ex);
               throw $ex;
               // Respond with HTTP 403 and display error page...
            }
            catch (Dropbox\WebAuthException_NotApproved $ex) {
               Log::info("Dropbox\WebAuthException_NotApproved raised in Dropbox::getCompletion");
               Log::error($ex);
               throw $ex;
			 }
            catch (Dropbox\WebAuthException_Provider $ex) {
               Log::info("Dropbox\WebAuthException_Provider raised in Dropbox::getCompletion");
               Log::error($ex);
               throw $ex;
            }
            catch (Dropbox\Exception $ex) {
               Log::info("Dropbox\Exception raised in Dropbox::getCompletion");
               Log::error($ex);
               throw $ex;
            }
    }
/************************************************************************************************/

	/*
	*	@params:
	*	userCloudID = ID of the user's cloud
	*	folderPath = Path to the folder to be downloaded
	*	@return value:
	* 	@Exceptions:	Exception
	*	@description: Path to the zip file created
	*					This function calls downloadFolderOnServer and passes an array by reference
	*/

	public function downloadFolder($userCloudID, $folderPath){
		 try{
		 		list($path, $folderName)=	Utility::splitPath($folderPath);
				$folder = FileModel::getFileAttributes($userCloudID, $path, $folderName,array('fileID'));
				$folderID = $folder->fileID;
				$this->getFolderWithDescendants($userCloudID, $folderPath);
				
				$client = $this->getClient($userCloudID);
				$array = array();
				$this->downloadFolderOnServer($userCloudID,$folderPath,$client,$array);
				$jsonFilePath = public_path().'/temp/dropbox/downloads/'.$folderID.'.json';
				File::put($jsonFilePath,json_encode($array));
				return Utility::createZip($jsonFilePath,self::$cloudName);

		}catch(Exception $e){
			Log::info("Exception raised in Dropbox::downloadFolder",array('userCloudID'=>$userCloudID,'folderPath'=> $folderPath));
			Log::error($e);
			throw $e;
		}
	}
/************************************************************************************************/

	/*
	*	@params:
	*	userCloudID = ID of the user's cloud
	*	folderPath = Path to the folder to be downloaded
	*	@return value: None
	* 	@Exceptions:	Exception
	*	@description: This function calls refreshFolder function on all child folders
	*					This is necessary since we might not have data about children when
	*					downloadFolder is called on a folder 
	*					
	*/

	private function getFolderWithDescendants($userCloudID, $folderPath){
		try{
			$folderContents = $this->getFolderContents($userCloudID, $folderPath);
			foreach ($folderContents as $file) {
				if($file['is_directory']==true){
					// recursive call
					$this->getFolderWithDescendants($userCloudID, Utility::joinPath($folderPath, $file['file_name']));
				}
			}

		}catch(Exception $e){
			Log::info("Exception raised in Dropbox::getFolderWithDescendants",array('userCloudID'=>$userCloudID,'folderPath'=> $folderPath));
			Log::error($e);
			throw $e;
		}

	}
/************************************************************************************************/
	/*
	*	@params:
	*	userCloudID = ID of the user's cloud
	*	folderPath = Path to the folder to be downloaded
	*	client = client object of class Dropbox/Client.php
	*	array = Passed by reference because we will make recursive calls to it
	*	@return value: None..array has been passed by reference which is what we need
	* 	@Exceptions:	Exception
	*	@description: the function downloadFolder calls this function to create an associative array whose
	*					elements are of the form folderPath => files
	*					This function recursively parses all the folders inside folder and their successors
	*					to create an array
	*					This array is created recursively.
	*/

	private function downloadFolderOnServer($userCloudID, $folderPath,$client, &$array){
		try{
				$serverDestinationPath = public_path().'/temp/dropbox/downloads/';
				$files = FileModel::getFolderContents($userCloudID, $folderPath);
				$array[$folderPath]=$files;
				if($files != null){
					foreach($files as $file){
						if($file['is_directory']==true){
							// Recursive call
							$this->downloadFolderOnServer($userCloudID, Utility::joinPath($folderPath, $file['file_name']), $client, $array);
						}
						else{
							$fileDestination = $serverDestinationPath.$file['fileID'];
							if(Temp::TempFileExists($file['fileID'])	){
								// Check if the file is up to date , so get the metadata from dropbox
								$fileMetaData = $client->getMetaData(Utility::joinPath($folderPath, $file['file_name']));
								// Check if the rev values are same , if they are then send this file , DO NOT download
								if($fileMetaData['rev']==$file['rev']){ //otherwise download the file
									continue;// Do nothing for this file, go to next one
								}
							}// otherwise download the file
							$fileStream = fopen($fileDestination, 'wb');
							$client->getFile(	Utility::joinPath($folderPath, $file['file_name']  ), $fileStream);
							Temp::addTempEntry($file['fileID']);
						}
					}
				}
		}catch(Exception $e){
			Log::info("Exception raised in Dropbox::downloadFolderOnServer",array('userCloudID'=>$userCloudID, 'folderPath'=>$folderPath));
			Log::error($e);
			throw $e;
		}

	}
/************************************************************************************************/

}
