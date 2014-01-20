<?php
class Dropbox implements CloudInterface{
	private static $clientIdentifier = "Project-Kumo";
	private static $cloudID = '1';	
	private static $cloudName = 'dropbox';// Do not change used by DownloadFolder 
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
				Log::info("Exception raised in Dropbox::upload");
				Log::error($e);				
				throw $e;
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
				Log::info("Exception raised in Dropbox::download",
					array('userID'=>$userID, 'cloudSourcePath'=>$cloudSourcePath, 'fileName'=>$fileName));
				Log::error($e);				
				throw $e;
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
			//$userID = '1';													// COMMENT THIS LATER
			//$user = User::find($userID); 									// UNCOMMENT THIS LATER
			try{
				$accessToken = UnifiedCloud::getAccessToken($userID, self::$cloudID );
				if($accessToken == null){
					error_log('Access token is null');
					throw new AccessTokenNotFoundException();
					return null;
				}else{
					$client = new Dropbox\Client($accessToken,self::$clientIdentifier);
					return $client;
				}
			}catch(Exception $e){
				Log::info("Exception raised in Dropbox::getClient",array('userID'=>$userID));
				Log::error($e);				
				throw $e;
			}

	}
/************************************************************************************************/
	/*
	*	@params:
	*	userID = ID of the user 
	* 	folderPath = Path of the folder whose contents have been sought
	*	For eg : if folderPath = /Projects/Unicloud then contents of Unicloud will be returned
	*	@return value: Meta data of the folder and its files and folders
	* 	@Exceptions:	Exception
	*/
	public function getFolderContents($userID, $folderPath){
		// Get client object
		 
		try{
			// $client = self::getClient($userID);
			// // Dropbox API requires that there should be no trailing slash except if it is root '/'
			// // Obtain fileMetaData from Dropbox
			// $fileMetaData=$client->getMetadataWithChildren($folderPath);
			// return $fileMetaData;
			
			Log::info('getHasUserFiles values returned',array('userID' => $userID,'cloud' => self::$cloudID, 'values' => UnifiedCloud::getHasUserFiles($userID,self::$cloudID)));

			if(UnifiedCloud::getHasUserFiles($userID,self::$cloudID) == false) {
				Log::info('running getFullFileStructure');
				$this->getFullFileStructure($userID);
			}

			$fileArrayJson = UnifiedCloud::getFolderContents($userID,self::$cloudID,$folderPath);


			return $fileArrayJson;

		}catch(Exception $e){
				Log::info("Exception raised in Dropbox::getClient",array('userID'=>$userID));
				Log::error($e);				
				throw $e;
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
				Log::info("Exception raised in Dropbox::createFolder",array('userID'=>$userID,'folderPath'=>$folderPath));
				Log::error($e);				
				throw $e;
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
				Log::info("Exception raised in Dropbox::delete",array('userID'=>$userID, 'path'=>$path));
				Log::error($e);				
				throw $e;
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	userID = ID of the user 
	*	@return value: Complete Information regarding files on user's cloud 
	* 	@Exceptions:	Exception
	*	@description:	This function stores complete information of a user's dropbox files 
	*					in database of UnifiedCloud
	*/


// To be called when a user adds a new cloud to UnifiedCloud
// At that time , we bring in all information from dropbox and save it to our database
// After this we just bring the delta (ie the changes that have been made) and reflect them in our database

	public function getFullFileStructure($userID){
		 try{
			$client = self::getClient($userID);
			// First null : cursor
			// Second null: path_prefix
			$i=0;
			do{
				$data = $client->getDelta(null,null);
				$hasMore = $data['has_more'];// if hasMore = true then we are supposed to call 
				// getDelta again so as to get more data
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
					UnifiedCloud::addFileInfo($fileName, $userID, self::$cloudID,$path,$file[1]['is_dir'],
					Utility::changeDateFormatToDBFormat($file[1]['modified']),$file[1]['size'],$file[1]['rev']);
					//Log::info('running foreach these many times',array('file' => $fileName));
				}

				$i++;
				//Log::Info('running DO these many times: ',array('i' => $i));
			}while($hasMore==true && $i<10);

			//update user cloud info table.
			UnifiedCloud::setHasUserFiles($userID,self::$cloudID,true);
			Log::info('setHasUserFiles=',array('get' => UnifiedCloud::getHasUserFiles($userID,self::$cloudID)));
			
			//return $data;

		}catch(Exception $e){
				Log::info("Exception raised in Dropbox::getFullFileStructure",array('userID'=>$userID));
				Log::error($e);				
				throw $e;
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	userID = ID of the user 
	*	@return value: Data received from dropbox when refresh is called 
	* 	@Exceptions:	Exception
	*	@description:
	*/

	/*
	*	This function is a way to catch up with the changes occuring to user's dropbox
	*	First, when user adds dropbox to his account, we bring in all his file Information
	*	from dropbox using function getFullFileStructure.
	*	However, to catch up with the changes occuring on the cloud , this function can be 
	*	repeatedly called so that only the changes will be sent by dropbox
	*/

	public function refreshFullFileStructure($userID){
		try{
			$client = self::getClient($userID);

			if(UnifiedCloud::getHasUserFiles($userID,self::$cloudID) == false) {
				$this->getFullFileStructure($userID);
				return;
			}

			$oldCursor = UnifiedCloud::getOldCursor($userID, self::$cloudID);

 			//Cursor  : A string that is used to keep track of your current state. 
			//On the next call pass in this value to return delta entries 
			//that have been recorded since the cursor was returned.
			// if oldCursor = null , directly null will be passed to function call delta
			// everything remains the same
			$i=0;
			do{
					$data = $client->getDelta($oldCursor,null);
					$hasMore = $data['has_more'];
					$cursor = $data['cursor'];
					UnifiedCloud::setNewCursor($userID, self::$cloudID, $cursor);
					$fileData = $data['entries'];
					$reset = $data['reset'];
					if($reset == true){
						 // If true, clear your local state before processing the delta entries. 
						 // reset is always true on the initial call to /delta (i.e. when no cursor is passed in). 
						 // Otherwise, it is true in rare situations, such as after server or account maintenance, 
						 // or if a user deletes their app folder
						UnifiedCloud::resetFileState($userID, self::$cloudID);
					}
					
					foreach ($fileData as $file) {
						if($file[1]==null){// Then that file has been deleted , so we need 
											// to update our database and delete that file
							// Dropbox treats its files as case insensitive but preserves the case 
							// ie is the file ABC.txt then it will be saved as abc.xml in db 
							// but to the user, it will appear to be ABC.txt
							// This is why $file[1] is (all letters lowercase) filename of the file 
							// This will work even if a folder is deleted 
							list($path, $fileName)=	Utility::splitPath($file[0]);
							$file_db = UnifiedCloud::getFileCaseInsensitive($userID, self::$cloudID, $path, $fileName);
							if($file_db!=null){
								$file_db->delete();
							}					
						}
						else{

							$completePath = $file[1]['path'];
							list($path, $fileName)=	Utility::splitPath($completePath);
							$file_db = UnifiedCloud::getFile($userID, self::$cloudID, $path, $fileName);
							if($file_db == null){// Does this file exist?
								// If no then, create such a file 
								UnifiedCloud::addFileInfo($fileName, $userID, self::$cloudID,$path,$file[1]['is_dir'],
								Utility::changeDateFormatToDBFormat($file[1]['modified']),$file[1]['size'],$file[1]['rev']);
							}
							else{
								// If yes ...ie the file exists but may have changed
								// Delete the old entry and create a new entry
								$file_db->delete();
								UnifiedCloud::addFileInfo($fileName, $userID, self::$cloudID,$path,$file[1]['is_dir'],
								Utility::changeDateFormatToDBFormat($file[1]['modified']),$file[1]['size'],$file[1]['rev']);
							}

						}
					}

				$i++;	
			}while($hasMore==true && $i<10);
			//return $data;

		}catch(Exception $e){
				Log::info("Exception raised in Dropbox::refreshFullFileStructure",array('userID'=>$userID));
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
	public function getRegistrationPage(){
        // Redirect user to app authentication page of dropbox
        // uri of authentication is to be obtained using object of WebAuth class 
        try{
	        $webauth =$this->getWebAuth(); 
	        $authorizeUrl = $webauth->start();
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
	        $clientIdentifier = "Project-Kumo";
	        $redirectUri = "http://localhost/UnifiedCloud/public/index.php/auth/dropbox";// This needs a Https link ..only localhost 
	                                                                    //is allowed for http
	        $csrfTokenStore = new Dropbox\ArrayEntryStore($_SESSION, 'date(format)ropbox-auth-csrf-token');
	        return new Dropbox\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore);
    	
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
    function getCompletion(){
        try {
                
            // Get access token of the user now he has authenticated our app

            //$_GET is an array of variables passed to the current script via the URL parameters.
                
                list($accessToken, $userId, $urlState) = $this->getWebAuth()->finish($_GET);
//              assert($urlState === null);  // Since we didn't pass anything in start()
                //$path =  app_path().'/accessToken.txt';
                //File::put($path, $accessToken);

                //Hard coding Dropbox id because this auth belongs to dropbox.
                UnifiedCloud::setAccessToken(Session::get('email'),self::$cloudID,$accessToken);

                return Redirect::route('dashboard');
                //return View::make('complete');
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
	*	userID = ID of the user 
	*	folderPath = Path to the folder to be downloaded
	*	@return value: 
	* 	@Exceptions:	Exception
	*	@description: Path to the zip file created 
	*					This function calls downloadFolderOnServer and passes an array by reference
	*/

	public function downloadFolder($userID, $folderPath){
		 try{
		 		list($path, $folderName)=	Utility::splitPath($folderPath);
				$folder = UnifiedCloud::getFile($userID, self::$cloudID, $path, $folderName );
				$folderID = $folder->fileID;	
				$client = self::getClient($userID);
				$array = array();
				$this->downloadFolderOnServer($userID,$folderPath,$client,$array);
				$jsonFilePath = public_path().'/temp/dropbox/downloads/'.$folderID.'.json';
				File::put($jsonFilePath,json_encode($array));
				return UnifiedCloud::createZip($jsonFilePath,self::$cloudName);

		}catch(Exception $e){
			Log::info("Exception raised in Dropbox::downloadFolder",array('userID'=>$userID,'folderPath'=> $folderPath));
			Log::error($e);
			throw $e;
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*	userID = ID of the user 
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

	private function downloadFolderOnServer($userID, $folderPath,$client, &$array){
		try{
				$serverDestinationPath = public_path().'/temp/dropbox/downloads/';
				$files = UnifiedCloud::getFolderContentsPrecise($userID, self::$cloudID, $folderPath);
				$array[$folderPath]=$files;
				if($files != null){
					foreach($files as $file){
						if($file['is_directory']==true){
							// Recursive call 
							$this->downloadFolderOnServer($userID, Utility::joinPath($folderPath, $file['file_name']), $client, $array);
						}
						else{
							$fileDestination = $serverDestinationPath.$file['fileID'];
							if(UnifiedCloud::TempFileExists($file['fileID'])	){
								// Check if the file is up to date , so get the metadata from dropbox
								$fileMetaData = $client->getMetaData(Utility::joinPath($folderPath, $file['file_name']));
								// Check if the rev values are same , if they are then send this file , DO NOT download
								if($fileMetaData['rev']==$file['rev']){ //otherwise download the file
									continue;// Do nothing for this file, go to next one
								}
							}// otherwise download the file 
							$fileStream = fopen($fileDestination, 'wb');
							$client->getFile(	Utility::joinPath($folderPath, $file['file_name']  ), $fileStream);
							UnifiedCloud::addTempEntry($file['fileID']);
						}
					}
				}
		}catch(Exception $e){
			Log::info("Exception raised in Dropbox::downloadFolderOnServer",array('userID'=>$userID, 'folderPath'=>$folderPath));
			Log::error($e);				
			throw $e;
		}

	}
/************************************************************************************************/
	
}