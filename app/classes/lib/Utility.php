<?php
class Utility {
	// This class contains small functions which are used throughout the code
	
/***********************************************************************************************/	
	/*
	*	@params:
	*		completePath : The path to be split 
	*					   For eg : completePath='/Project/UniCloud/file.txt' will be split into 
	*					   $path = '/Project/UniCloud'
	*					   $fileName = 'file.txt'
	*					   Exceptional case: When completePath='/file.txt' will be split into
	*		     		   $path = '/'
	*					   $fileName = 'file.txt'
	*	@return value:
	*	 	($path, $fileName): 
	*	@decription : Splits the complete path to a file 
	*				
	*/
 	public static function splitPath($completePath){
		$pieces = explode('/',$completePath);
		$sizeOfPieces = count($pieces);
		$fileName = $pieces[$sizeOfPieces-1];
		$path = implode('/',array_slice($pieces, 0,-1)	);
		if($path == ''){	// Handling the exceptional case of /
			$path = '/';
		}
		return array($path, $fileName);
 	}
 	public static function joinPath($path,$fileName){
 		if($path == '/'){
 			return $path.$fileName;
 		}
 		else{
 			return $path.'/'.$fileName;
 		}
 	}
/***********************************************************************************************/	
	/*
	*	@params:
	*		path : The path to the folder 
	*					For eg: /home/Docs will delete all files in Docs folder 
	*				and then delete the folder itself
	*	@return value:
	*	 	None
	*	@decription : Removes all files within the folder and then 
	*				  deletes the folder
	*
	*/
 	public static function removeDir($path) {
	    // Normalise $path.
	    $path = rtrim($path, '/') . '/';
	    // Remove all child files and directories.
	    $items = glob($path . '*');
	    foreach($items as $item) {
	        is_dir($item) ? removeDir($item) : unlink($item);
	    }
	    // Remove directory.
	    rmdir($path);
	}
/***********************************************************************************************/	
	/*
	*	@params:
	*		dateString : date in string
	*	@return value:
	*	 	string : date in the format of Database which is YYYY-MM-DD HH:MM:SS
	*	@decription : Changes the format of a date string and returns the date string
	*				  in Database format 	
	*
	*/
	public static function changeDateFormatToDBFormat($dateString){
		$date = new DateTime($dateString);
		return $date->format('Y-m-d H:i:s');
	}
/**********************************************************************************************/

	/*
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
						throw new Exception('Zipped file could not be opened in Utility::createZip');
				}
			
				// Add files and folders to zip 
		        foreach($newFileArray as $folderPath => $files){
	//TODO Surbhi Issue: this function does not add empty directories				 	
		        	//$zip->addEmptyDir($folderPath);
				 	foreach ($files as  $file) {
				 		$fileLocation  = $filesDestination.$file['fileID'];
				 		if($file['is_directory']==false && file_exists($fileLocation) == false){
				 			Log::info("File does not exist in Utility::createZip",array('file/folder'=>$file['file_name'] , 'fileLocation'=>$fileLocation));
				 			throw new Exception('File does not exist');		
				 		}
				 		$zip->addFile($fileLocation, $folderPath.'/'.$file['file_name']);
				 	}
				 	//Log::info("Adding to zip ",array('folderPath',$folderPath));
				 	
				 }	
				$zip->close();
				return $zipFileName;
		}	

/***********************************************************************************************/	
	/*
	*	@params:
	*		Array : array of values from DB
	*	@return value:
	*	 	string : json string
	*	@decription : Converts php array to json string, because built in json encode needs
	*					UTF-* encoded string to convert array to json.	 	
	*
	*/

	public static function arrayJsonEncode($val)
	{
	    if (is_string($val)) return '"'.addslashes($val).'"';
	    if (is_numeric($val)) return $val;
	    if ($val === null) return 'null';
	    if ($val === true) return 'true';
	    if ($val === false) return 'false';

	    $assoc = false;
	    $i = 0;
	    foreach ($val as $k=>$v){
	        if ($k !== $i++){
	            $assoc = true;
	            break;
	        }
	    }
	    $res = array();
	    foreach ($val as $k=>$v){
	        $v = self::arrayJsonEncode($v);
	        if ($assoc){
	            $k = '"'.addslashes($k).'"';
	            $v = $k.':'.$v;
	        }
	        $res[] = $v;
	    }
	    $res = implode(',', $res);
	    return ($assoc)? '{'.$res.'}' : '['.$res.']';
	}
/**********************************************************************************************/
}


