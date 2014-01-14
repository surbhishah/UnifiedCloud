<?php
class Utility {
	public static function arrayToObject($d) {
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __METHOD__ 
			* __METHOD__ = current method
			* for recursive call
			*/
			return (object) array_map(__METHOD__, $d);
		}
		else {
			// Base case of recursion
			return $d;
		}
	}
 	
 	public static function splitPath($completePath){
		$pieces = explode('/',$completePath);
		$sizeOfPieces = count($pieces);
		$fileName = $pieces[$sizeOfPieces-1];
		$path = implode('/',array_slice($pieces, 0,-1)	);
		if($path == ''){
			$path = '/';
		}
		return array($path, $fileName);
 	}

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

}