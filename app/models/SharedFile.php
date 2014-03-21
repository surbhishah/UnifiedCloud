<?php

class SharedFile extends Eloquent  {

	protected $table = 'shared_files';
	protected $primaryKey = 'shared_fileID';
/**********************************************************************************************/	
	public function owner()
    {
    	//User:	Model name 
    	//userID : foreign key
    	//ownerID : local key
        return $this->belongsTo('User','ownerID','userID');
    }

    public function sharer(){
    	return $this->belongsTo('User','sharerID','userID');
    }
    
	public function file(){
		return $this->belongsTo('FileModel','fileID','fileID');
	}    
/**********************************************************************************************/	
	
	public static function removeSharing($sharedFileID){
		SharedFile::destroy($sharedFileID);
	}
/**********************************************************************************************/	
	/*public static function setAccessRights($sharedFileID,$accessRights){
		$sharedFile = SharedFile::find($sharedFileID);
		$sharedFile->access_rights = $accessRights;
		$sharedFile->save();
	}
*//**********************************************************************************************/	
	public static function getFile($sharedFileID){
		$file = SharedFile::find($sharedFileID)->file;
		return $file;
	}
/**********************************************************************************************/	
	public static function createSharedFile($fileID, $ownerID, $sharerID){
		$sharedFile = new SharedFile;
		$sharedFile->fileID = $fileID;
		$sharedFile->ownerID = $ownerID;
		$sharedFile->sharerID = $sharerID;
		$sharedFile->save();
	}
/**********************************************************************************************/	
	public static function getFilesSharedByUser($ownerID){
		return DB::select('
			SELECT shared_fileID, file_name, shared_files.created_at, first_name,last_name,email
			FROM shared_files
			LEFT JOIN users on (shared_files.sharerID = users.userID)
			LEFT JOIN files on (shared_files.fileID = files.fileID)
			WHERE shared_files.ownerID = ?
			', array($ownerID));
        
        }
/**********************************************************************************************/	
        public static function getFilesSharedWithUser($sharerID){
        	return DB::select('
        		SELECT shared_fileID, file_name, shared_files.created_at, first_name,last_name,email
				FROM shared_files
				LEFT JOIN users on (shared_files.ownerID = users.userID)
				LEFT JOIN files on (shared_files.fileID = files.fileID)
				WHERE sharerID = ?
        		', array($sharerID));
        }
/**********************************************************************************************/	
}






