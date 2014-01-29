<?php

class FilesController extends BaseController{
	public $restful = true;	
/************************************************************************************************/
	/*
	*	@params : 
	*		files: The file(s) to be uploaded, multiple files are also allowed
	*		cloudName: Name of the cloud to which file is to be uploaded (case insensitive)
	*		cloudDestinationPath: Path where file is to be uploaded , excluding the name of the file 
	*		For eg : if cloudDestinationPath =  '/Project/Files' then file will be uploaded at '/Project/Files/[filename]'
	*	@return value: None
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	// Upload	
	public function postFile($cloudName){
		try{
			
			$cloudName = Input::get('cloudName');
			$userCloudID = Input::get('userCloudID');		
			$cloudDestinationPath = Input::get('cloudDestinationPath');
			$factory = new CloudFactory(); 
			$cloud = $factory->createCloud($cloudName);
			$i=0;// TO BE COMMENTED GEtting $result is not necessary ,,,delete it later abhishek
			$files = Input::file('files');
			foreach($files as $file){
				$result[$i]= $cloud->upload($userCloudID, $file, $cloudDestinationPath);	
				$i++;
			}
			return $result;//THIS is required only for testing
				
		}catch(UnknownCloudException $e){
				Log::info("UnknownCloudException raised in FilesController::postUploadMultiple");
				Log::error($e->getMessage());
				throw $e;

		}catch(Exception $e){
				Log::info("Exception raised in FilesController::postUploadMultiple");
				Log::error($e->getMessage());
				throw $e;
		}	
			
	}
/************************************************************************************************/	
	/*	@params : 
	*		userCloudID : Id of the user's cloud
	*		fileName : name of the file to be downloaded
	*		cloudName: Name of the cloud to which file is to be uploaded (case insensitive)
	*		cloudSourcePath: Path from where file is to be downloaded , excluding the name of the file 
	*		For eg : if cloudSourcePath =  '/Project/Files/' then file will be downloaded from '/Project/Files/[filename]'
	*	@return value:
	*		file to be downloaded by user
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	// Download
	public function getFile(){
			try{
				$userCloudID = Input::get('userCloudID');
				$cloudSourcePath=Input::get('cloudSourcePath');
			 	$fileName = Input::get('fileName');
			 	$cloudName = Input::get('cloudName');// to be COMMENTED later abhishek if cloudName is passed as parameter 
				$factory = new CloudFactory(); 			// to controller 
				$cloud = $factory->createCloud($cloudName);
				$fileDestination =$cloud->download($userCloudID, $cloudSourcePath, $fileName);			
				// Return the file with the response so that browser shows an option to user to download a file
				return Response::download($fileDestination,$fileName);

			}catch(UnknownCloudException $e){
				Log::info("UnknownCloudException raised in FilesController::getFile");
				Log::error($e->getMessage());
				throw $e;

			}catch(Exception $e){
				Log::info("Exception raised in FilesController::getFile");
				Log::error($e->getMessage());
				throw $e;
			
			}		
	}
/************************************************************************************************/
	/*
	*	@params : 
	*		folderPath : The path to folder whose contents information is required 
	*		For eg: if folderPath = '/Project/Subproject/' then 
	*		Contents of Subproject will be returned 
	*		cloudName: Name of the cloud (case insensitive)
	*	@return value: The contents of folder after updation of database
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	public function getFolderContents(){
		try{
			$cloudName = Input::get('cloudName');
			$folderPath = Input::get('folderPath'); 
			$userCloudID = Input::get('userCloudID');
			$cached = Input::get('cached');
			$factory = new CloudFactory(); 
			$cloud = $factory->createCloud($cloudName);
			$result=$cloud->getFolderContents($userCloudID, $folderPath,$cached);
			
			$jsonResult = Utility::arrayJsonEncode($result);
			return $jsonResult;

			

		}catch(UnknownCloudException $e){
				Log::info("UnknownCloudException raised in FilesController::getFile");
				Log::error($e->getMessage());
				throw $e;

		}catch(Exception $e){
				Log::info("Exception raised in FilesController::getFolderContents");
				Log::error($e->getMessage());
				throw $e;
			
		}
	}
/************************************************************************************************/
	/*
	*	@params:
	*		$cloudName = Name of the cloud (case insensitive)
	*		$folderPath = complete path to the folder 
	*		Eg : /Project/Subproject/NewFolder/ will create a folder named NewFolder
	*	@return value : Null when folder could not be created
	*					Otherwise metadata of the folder 
	*	@Exceptions: UnknownCloudException, Exception
	*/
	public function getCreateFolder(){
		try{
		
			$cloudName = Input::get('cloudName');
			$folderPath= Input::get('folderPath');
			$userCloudID = Input::get('userCloudID');
			$factory = new CloudFactory(); 
			$cloud = $factory->createCloud($cloudName);
			$result = $cloud->createFolder($userCloudID, $folderPath);

			//if $result == null then folder already exists
			return View::make('complete')->with('message',$result);	

		}catch(UnknownCloudException $e){
				Log::info("UnknownCloudException raised in FilesController::getCreateFolder");
				Log::error($e->getMessage());
				throw $e;

		}catch(Exception $e){
				Log::info("Exception raised in FilesController::getCreateFolder");
				Log::error($e->getMessage());
				throw $e;
			
		}
	}

/************************************************************************************************/
	/*
	*	@params : 
	*		path : path to the file to be deleted  
	*		For eg: if path = '/Project/Subproject/file.txt' then 
	*		file.txt will be deleted
	*		cloudName: cloud from which file will be deleted (case insensitive)
	*	@return value: 
	*		None, can return metadata of deleted file if required
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	public function delete($cloudName, $path){
		try{
			$cloudName = Input::get('cloudName');
			$path= Input::get('path');
			$userCloudID =  Input::get('userCloudID');

			$factory = new CloudFactory(); 
			$cloud = $factory->createCloud($cloudName);
			$result = $cloud->delete($userCloudID,$path);
			return View::make('complete')		
						->with('message',$result);// Needed only for testing the functioning, May be COMMENTED
		
		}catch(UnknownCloudException $e){
				Log::info("UnknownCloudException raised in FilesController::delete");
				Log::error($e->getMessage());
				throw $e;

		}catch(Exception $e){
				Log::info("Exception raised in FilesController::delete");
				Log::error($e->getMessage());
				throw $e;
			
		}
	}
/************************************************************************************************/
	/*
	*	@params : GET parameters
	*		cloudName: Name of the cloud to be added 
	*		userCloudID = ID of the user 's cloud
	* 		folderPath= Path to the folder to be downloaded as zip 
	*	@return value: returns zip file to be downloaded by user 
	*	@description:	This function is called when user wants to download a folder 
	*					It returns the folder as a zip and deleted the zip file later from our server
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	public function getDownloadFolder(){
		try{
			
			$cloudName = Input::get('cloudName');
			$userCloudID = Input::get('userCloudID');		
			$folderPath = Input::get('folderPath');
			$factory = new CloudFactory();
			$cloud = $factory->createCloud($cloudName);
			$zipFileName = $cloud->downloadFolder($userCloudID,$folderPath);
			header('Content-Type: application/zip');
			header('Content-disposition: attachment; filename='.$zipFileName);
			readfile($zipFileName);
			unlink($zipFileName);
		
		}catch(UnknownCloudException $e){
				Log::info("UnknownCloudException raised in FilesController::getDownloadFolder");
				Log::error($e->getMessage());
				throw $e;

		}catch(Exception $e){
				Log::info("Exception raised in FilesController::getDownloadFolder");
				Log::error($e->getMessage());
				throw $e;
			
		}

	}
/************************************************************************************************/
	
}
