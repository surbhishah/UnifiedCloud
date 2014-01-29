<?php

class SharedFile extends Eloquent  {

	protected $table = 'shared_files';
	protected $primaryKey = 'shared_fileID';
/**********************************************************************************************/	
	public static function createSharedFile($fileID, $ownerID, $sharerID, $accessRights){
		$sharedFile = new SharedFile;
		$sharedFile->fileID = $fileID;
		$sharedFile->ownerID = $ownerID;
		$sharedFile->sharerID = $sharerID;
		$sharedFile->access_rights = $accessRights;//  accessRights has to be either R or RW , 
		$sharedFile->save();						// if you pass anything else,
		return $sharedFile;							// then null is added to db 
	}
/**********************************************************************************************/	
	public static function getFilesSharedByUser($ownerID){
		return SharedFile::where('ownerID','=',$ownerID);
	}	
/**********************************************************************************************/	
	public static function getFilesSharedByUser($sharerID){
		return SharedFile::where('sharerID','=',$sharerID);
	}
/**********************************************************************************************/	
	public static function removeSharing($sharedFileID){
		SharedFile::destroy($sharedFileID);
	}
/**********************************************************************************************/	
}