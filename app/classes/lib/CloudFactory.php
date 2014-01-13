<?php
class CloudFactory{
	private $cloud;
	public function createCloud($cloudName){
		if(Str::lower($cloudName) == 'dropbox'){
			$cloud = new Dropbox();
		}
		else if(Str::lower($cloudName) == 'googledrive'){
			$cloud = new GoogleDrive();
		}
		else if(Str::lower($cloudName)	=='skydrive'){
			$cloud = new SkyDrive();
		}
		else{
			$cloud = null;
			throw new UnknownCloudException();
		}
		return $cloud;
	}
}
