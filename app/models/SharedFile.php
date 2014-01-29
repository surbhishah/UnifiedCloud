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
	public static function getFilesSharedByUser($ownerID){//TODO SURBHI
		DB::table('shared_files')
			->join('files','shared_files.fileID','=','files.fileID')
			->join('users','users.userID','=','shared_files.sharerID')
			->select('shared_files.shared_fileID','files.file_name','users.first_name',
				'users.last_name','shared_files.sharerID', 'shared_files.access_rights');

		return SharedFile::where('ownerID','=',$ownerID);
	}	
/**********************************************************************************************/	
	public static function getFilesSharedWithUser($sharerID){
		return SharedFile::where('sharerID','=',$sharerID);
	}
/**********************************************************************************************/	
	public static function removeSharing($sharedFileID){
		SharedFile::destroy($sharedFileID);
	}
/**********************************************************************************************/	
}