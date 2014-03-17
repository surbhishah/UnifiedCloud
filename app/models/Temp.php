<?php

class Temp extends Eloquent  {

	protected $table = 'temp';
	protected $primaryKey = 'fileID';
	public function file(){
		$this->belongsTo('FileModel','fileID','fileID');
	}
	/**********************************************************************************************/
	/*
	*	@params:
	*		fileID : ID of the file
	*	@return value:
	*	 	None
	*	@decription : Adds a new entry to Temp table
	*				  Temp table is supposed to hold entries of files which are already present on server
	*	
	*/
	public static function addTempEntry($fileID){
		$temp = new Temp();
		$temp->fileID = $fileID;
		$temp->save();
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		fileID : ID of the file
	*	@return value:
	*	 	Boolean : true if temp entry exists otherwise false
	*	@decription : Adds a new entry to Temp table
	*				  Temp table is supposed to hold entries of files which are already present on server
	*	
	*/
	public static function TempFileExists($fileID){
		$file = Temp::where('fileID','=',$fileID)->get()->first();
		if($file ==NULL )return false;
		else return true;
	}

	
}