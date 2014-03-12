<?php


class Cloud extends Eloquent  {

	protected $table = 'clouds';
	protected $primaryKey = 'cloudID';

	public function userClouds(){
		$this->hasMany('UserCloudInfo','cloudID','cloudID');
	}
	
	public static function getCloudName($cloudID){
		return Cloud::find($cloudID)->name;
	}
		
	
	
}