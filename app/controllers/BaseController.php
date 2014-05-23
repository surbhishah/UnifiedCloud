<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
	protected function getCloudInstance($cloudName){
		$factory = new CloudFactory();
		$cloud = $factory->createCloud($cloudName);
		return $cloud;
	}
}