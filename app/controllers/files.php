<?php

class FilesController extends BaseController{
	public $restful = true;	
/************************************************************************************************/
	/*
	*	@params : 
	*	userFile: The file to be uploaded
	*	cloudName: Name of the cloud to which file is to be uploaded (case insensitive)
	*	cloudDestinationPath: Path where file is to be uploaded , excluding the name of the file 
	*	For eg : if cloudDestinationPath =  '/Project/Files' then file will be uploaded at '/Project/Files/[filename]'
	*
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	// Upload	
	public function postFile($cloudName){

		if(Input::hasFile('userfile')){
			try{
				$cloudName =  Input::get('cloudName');// TO BE COMMENTED LATER ABHISHEK
				$userCloudID = Input::get('userCloudID');
				$factory = new CloudFactory(); 
				$cloud = $factory->createCloud($cloudName);
				$result =$cloud->upload($userCloudID,
										Input::file('userfile'),
										Input::get('cloudDestinationPath'));			
				return $result;
			
			}catch(UnknownCloudException $e){
				Log::info("UnknownCloudException raised in FilesController::postFile");
				Log::error($e->getMessage());
				throw $e;
			}catch(Exception $e){
				Log::info("Exception raised in FilesController::postFile");
				Log::error($e->getMessage());
				throw $e;
				
			}

		}
		else {
//TODO			
			return View::make('complete')
						->with('message','Error:::Uploaded file not found');
		}
			
	}
/************************************************************************************************/	
	/*	@params : 
	*	fileName : name of the file to be downloaded
	*	cloudName: Name of the cloud to which file is to be uploaded (case insensitive)
	*	cloudSourcePath: Path from where file is to be downloaded , excluding the name of the file 
	*	For eg : if cloudSourcePath =  '/Project/Files/' then file will be downloaded from '/Project/Files/[filename]'
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	// Download
	public function getFile(){
			try{
				$userCloudID = Input::get('userCloudID');
				$cloudSourcePath=Input::get('cloudSourcePath');
			 	$fileName = Input::get('fileName');
			 	$cloudName = Input::get('cloudName');// to be COMMENTED later abhishek if cloudName is passed as parameter
				$factory = new CloudFactory(); 
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
	*	folderPath : The path to folder whose contents information is required 
	*	For eg: if folderPath = '/Project/Subproject/' then 
	*	Contents of Subproject will be returned 
	*	cloudName: Name of the cloud (case insensitive)
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	public function getFolderContents(){
		try{
			$cloudName = Input::get('cloudName');
			$folderPath = Input::get('folderPath'); 
			$userCloudID = Input::get('userCloudID');
			$factory = new CloudFactory(); 
			$cloud = $factory->createCloud($cloudName);
			$result=$cloud->getFolderContents($userCloudID, $folderPath);
			return View::make('complete')->with('message',$result);
			

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
	*	$cloudName = Name of the cloud (case insensitive)
	*	$folderPath = complete path to the folder 
	*	Eg : /Project/Subproject/NewFolder/ will create a folder named NewFolder
	*	@return value : Null when folder could not be created
	*	Otherwise metadata of the folder 
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
	*	path : path to the file to be deleted  
	*	For eg: if path = '/Project/Subproject/file.txt' then 
	*	file.txt will be deleted
	*	cloudName: cloud from which file will be deleted (case insensitive)
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

			//if $result == null then folder already exists
			return View::make('complete')
						->with('message',$result);
		
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
	*	cloudName: Name of the cloud to be added 
	*	userCloudID = ID of the user 's cloud
	* 	folderPath= Path to the folder to be downloaded as zip 
	*	@return value: returns zip file to be downloaded by user 
	*	@description:	This function is called when user wants to download a folder 
	*					It returns the folder as a zip and deleted the zip file later 
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
	/*
	*	@params : GET parameters
	*	cloudName: Name of the cloud to be added 
	*	userCloudID = ID of the user's cloud
	* 	files = array which holds multiple files 
	*	@return value: returns zip file to be downloaded by user 
	*	@description:	This function is called when user wants to upload multiple files
	*	@Exceptions:UnknownCloudException,	Exception
	*/
	public function postUploadMultiple(){
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
			return $result;
				
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

}
