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
/***********************************************************************************************/	
}


