<?php


class FileModel extends Eloquent  {

	protected $table = 'files';
	protected $nullable = 'hash';
	protected $primaryKey = 'fileID';

/**********************************************************************************************/	
	/*
	*	@params:
	*		fileName = Name of the file along with extension Eg : file.txt 
	*		userCloudID : ID of the user 's cloud
	*		path = Path to the file For eg: /Project/UniCloud for /Project/UniCloud/file.txt
	*		isDirectory = true if the file is actually a directory otherwise false
	*		lastModifiedTime=  Last Modified Time in YYYY-MM-DD HH:MM:SS format ie the database format
	*		size = size of the file
	*		rev = revision of the file 
	*		isEncrypted = is this parameter is not passed , the default value of false will be taken
	*	@return value:
	*	 	None
	*	@decription : Adds a new file to app database 
	*
	*/
	public static function addOrUpdateFile($userCloudID, $fileArray){
		$file = self::getFile($userCloudID, $fileArray['path'], $fileArray['fileName']);
		if($file == null){
			$file = new FileModel();
		}
		$file->user_cloudID = $userCloudID;
		$file->path = $fileArray['path'];
		$file->is_encrypted = false;
		$file->encryption_key_hash = null;
		$file->file_name = $fileArray['fileName'];
		$file->last_modified_time= Utility::changeDateFormatToDBFormat($fileArray['lastModifiedTime']);
		$file->is_directory= $fileArray['isDirectory'];
		$file->rev = $fileArray['rev'];
		$file->size = $fileArray['size'];
		$file->hash = $fileArray['hash'];
		$file->save();

	}
/**********************************************************************************************/	
	/*
	*	@params:
	*		userCloudID : ID of the user 's cloud
	*		path : Path to the file For eg /Project/UniCloud for a file at /Project/UniCloud/file.txt
	*		fileName:	Name of the file For eg file.txt
	*	@return value:
	*	 	object of class FileModel
	*/
	public static function getFile($userCloudID, $path, $fileName){
			return FileModel::where('user_cloudID','=',$userCloudID)
							->where('path','=',$path)->where('file_name','=',$fileName)
							->get()->first();
	}
/**********************************************************************************************/	
	/*
	*	@params:
	*		userCloudID : ID of the user 's cloud
	*		path : Path to the file For eg /Project/UniCloud for a file at /Project/UniCloud/file.txt
	*		fileName:	Name of the file For eg file.txt
	*		attributes: array of attributes to be fetched and returned
	*	@return value:
	*	 	fileModel object with specified attributes
	*	@decription : Returns the attributes of file of a user at a particular path on the cloud 
	*
	*/
	public static function getFileAttributes($userCloudID, $path, $fileName, $attributes){
			return FileModel::where('user_cloudID','=',$userCloudID)
							->where('path','=',$path)->where('file_name','=',$fileName)
							->select($attributes)->get()->first();
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userCloudID : ID of the user 's cloud
	*		path : Path to the file For eg /Project/UniCloud for a file at /Project/UniCloud/file.txt
	*		fileName:	Name of the file For eg file.txt
	*	@return value:
	*	 	None
	*	@decription : Deleted the file at user's cloud 
	*
	*/
	public static function deleteFile($userCloudID, $path, $fileName){
		FileModel::where('user_cloudID','=',$userCloudID)->where('path','=',$path)
					->where('file_name','=',$fileName)->delete();
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userCloudID: ID of the user's cloud
	*		fullPath: The path to the folder or file whose hash is required
	*					In dropbox, files do not have hash
	*	@return value:
	*	 	Boolean : hash of the file/folder 
	*/
	public static function getHash($userCloudID, $fullPath){
		list($path, $fileName)= Utility::splitPath($fullPath);
		$file= FileModel::where('user_cloudID','=',$userCloudID)->where('path','=',$path)
						->where('file_name','=',$fileName)->get()->first();			
		if($file==null)return null;
		else return $file->hash;
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userCloudID : ID of the user 
	*		path : 	Path to the folder 
	*				For eg if path = '/Project/UniCloud'
	*				The function shall return the contents of this folder 
	*	@return value:
	*				an Array of files each containing fileName, last_modified_time, isDirectory and size of the file
	*	@decription : Returns the file(s) of a user at a particular path on the cloud 
	*
	*/
	public static function getFolderContents($userCloudID, $path){
		return FileModel::where('user_cloudID','=',$userCloudID)->where('path','=',$path)->get()->toArray();
		// I am making this function generic and not specifically getting particular attributes 
		// so that when db caches this query , it will be more effective
		// Also, may functions require FolderContents in different attributes
		// It is in my view better not to make similar functions , just returning
		// different attributes
//				->select(array('file_name','last_modified_time','is_directory','size'))->get()->toArray();
		//toJson() can also be used in place of toArray 

	}	
/**********************************************************************************************/
	public static function setEncryptionKeyHash($userCloudID,$fileName,$path,$encryptionKeyHash) {
		$file =  FileModel::where('user_cloudID','=',$userCloudID)
							->where('file_name','=',$fileName)
							->where('path','=',$path)->get()->first();

		$file->is_encrypted = true;
		$file->encryption_key_hash = $encryptionKeyHash;
		$file->save();
	}

	public static function getEncryptionKeyHash($userCloudID,$fileName,$path) {
		$encryptionKeyHash = FileModel::where('user_cloudID','=',$userCloudID)
							->where('file_name','=',$fileName)
							->where('path','=',$path)
							->select('encryption_key_hash')
							->get()->first();
		return $encryptionKeyHash->encryption_key_hash;
	}
	
}