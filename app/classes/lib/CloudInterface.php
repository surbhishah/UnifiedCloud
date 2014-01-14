<?php
interface CloudInterface{
	public function upload($userID, $userfile, $cloudDestinationPath);
	public function download($userID, $cloudSourcePath,$fileName);

}