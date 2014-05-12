<?php
class CloudFactory{
	private $cloud;
	public function createCloud($cloudName){
		try{
			$fileName = app_path()."/clouds.json";
			$json_string = file_get_contents($fileName);
			$json = json_decode($json_string);
			$clouds = $json->clouds;
			$clouds_lowerCase = array_map("strtolower", $clouds);
			$index = array_search(strtolower($cloudName), $clouds_lowerCase);
			if($index === FALSE)
				throw new UnknownCloudException();
			
			$className = $clouds[$index];
			$ref = new ReflectionClass($className);
			$classInstance = $ref->newInstance();
			return $classInstance;

		}catch(ReflectionException $e){
			Log::info("Exception raised in CloudFactory",array('CloudName'=>$cloudName));
 			Log::error($e);
 			throw $e;
		}
	}
}
