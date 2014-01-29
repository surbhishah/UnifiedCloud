<?php

class EncryptionController extends BaseController {

	
	public function postTestGet() {

		$files = Input::file('files');

		foreach ($files as $file) {
			$file = $file->openFile('rb');
			$fileContents = '';
			while (!$file->eof()) {
				$fileContents .= $file->fgets();
			}
			return View::make('complete')->with('message',$file->isWritable());
		}
		
	}	

    public function postEncryptFiles($cloudName){
		try{
			
			$cloudName = Input::get('cloudName');
			$userCloudID = Input::get('userCloudID');		
			$cloudDestinationPath = Input::get('cloudDestinationPath');

			//Send passKey by POST
			//passKey required to encrypt randomly generated Encryption key. 
			$userPassKey = Input::get('passKey');

			$i=0;// TO BE COMMENTED GEtting $result is not necessary ,,,delete it later abhishek
			$files = Input::file('files');
			foreach($files as $file){

				$result[$i] = $this->encryptFile($cloudName,$userCloudID,$cloudDestinationPath,$file,$userPassKey);
				//$result[$i]= $cloud->upload($userCloudID, $file, $cloudDestinationPath);	
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

	private function encryptFile($cloudName,$userCloudID,$cloudDestinationPath,$file,$userPassKey) {
		try {

			//get random pass key
		    $randomPassKey = Encryption::generateRandomString();
		    
		    //get filename
		    $fileName = $file->getClientOriginalName();

		    //reading file contents for encryption
		    $fileObject = $file->openFile('rb');
		    $fileContents = '';
		    while (!$fileObject->eof()) {
		        $fileContents .= $fileObject->fgets();
		    }
		    //close file object
		    $fileObject = null;

		    //encrypt file with random pass key
		    $encFileContents = Encryption::encrypt($fileContents,$randomPassKey);

		    //overwrite non-encrypted file with new contents.
		    $fileObject = $file->openFile('wb');
		    $fileObject->fwrite($encFileContents);
		    $fileObject = null;

		    //uploading encrypted file.
		    $factory = new CloudFactory(); 
			$cloud = $factory->createCloud($cloudName);
			$result = $cloud->upload($userCloudID, $file, $cloudDestinationPath);	
			
		    //encrypting randomPassKey with userPassKey
		    $randomPassKeyHash = Encryption::encrypt($randomPassKey,$userPassKey);

		    //store $randomPassKeyHash in files table
		    FileModel::setEncryptionKeyHash($userCloudID,$fileName,$cloudDestinationPath,$randomPassKeyHash);

		    return $result;

		} catch(UnknownCloudException $e){
				Log::info("UnknownCloudException raised in FilesController::postUploadMultiple");
				Log::error($e->getMessage());
				throw $e;

		}catch(Exception $e){
				Log::info("Exception raised in FilesController::postUploadMultiple");
				Log::error($e->getMessage());
				throw $e;
		}

	}

	public function postDownloadEncryptedFile(){
			try{
				$userCloudID = Input::get('userCloudID');
				$cloudSourcePath=Input::get('cloudSourcePath');
			 	$fileName = Input::get('fileName');
			 	$cloudName = Input::get('cloudName');// to be COMMENTED later abhishek if cloudName is passed as parameter 
				$userPassKey = Input::get('passKey');

				$factory = new CloudFactory(); 			// to controller 
				$cloud = $factory->createCloud($cloudName);
				$fileDestination =$cloud->download($userCloudID, $cloudSourcePath, $fileName);			
				// Return the file with the response so that browser shows an option to user to download a file
				
				//decrpyting file 
				//get encryptionKeyHash
				$encryptionKeyHash = FileModel::getEncryptionKeyHash($userCloudID,$fileName,$cloudSourcePath);
				//Log::info('encryptionKey',array('encKey'=>$encryptionKeyHash));
				//decrypt encryption key
				$encryptionKey = Encryption::decrypt($encryptionKeyHash,$userPassKey);

				//decrypt file
				//get file contents
				$fileContents = file_get_contents($fileDestination);
				return View::make('complete')->with('message',$fileContents);
				//decrypting file contents
				$decryptedFileContents = Encryption::decrypt($fileContents,$encryptionKey);

				//ASK Surbhi should I use SplFileInfo to create descriptor as it is OOP.
				//write original file contents to file
				$fileDescriptor = fopen($fileDestination,'wb');
				fwrite($fileDescriptor, $decryptedFileContents);
				fclose($fileDescriptor);

				return Response::download($fileDestination,$fileName);
				//return $encryptionKey;

				//change genRandomKey to 32 length.
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

}