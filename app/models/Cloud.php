<?php


class Cloud extends Eloquent  {

	protected $table = 'clouds';
	protected $primaryKey = 'cloudID';

	public static function getCloudName($cloudID){
		return Cloud::find($cloudID)->name;
	}
	
	
}