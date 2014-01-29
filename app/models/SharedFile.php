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
		return DB::table('shared_files')
			->where('ownerID','=', $ownerID)
			->join(	'files' , 	'shared_files.fileID'	, '=' , 'files.fileID' )
			->join(	'users',	'shared_files.sharerID',	'='	,	'users.userID')

			->select(	'shared_fileID'	,	'file_name'	,	'first_name',
				'last_name', 'access_rights')
			->get();
	}	
/**********************************************************************************************/	
	public static function getFilesSharedWithUser($sharerID){
		return DB::table('shared_files')
			->where('sharerID','=', $sharerID)
			->join(	'files' , 	'shared_files.fileID'	, '=' , 'files.fileID' )
			->join(	'users',	'shared_files.ownerID',	'='	,	'users.userID')

			->select(	'shared_fileID'	,	'file_name'	,	'first_name',
				'last_name'	, 'access_rights')
			->get();	
	}
/**********************************************************************************************/	
	public static function removeSharing($sharedFileID){
		SharedFile::destroy($sharedFileID);
	}
/**********************************************************************************************/	
	public static function setAccessRights($sharedFileID,$accessRights){
		$sharedFile = SharedFile::find($sharedFileID);
		$sharedFile->access_rights = $accessRights;
		$sharedFile->save();
	}
/**********************************************************************************************/	

}