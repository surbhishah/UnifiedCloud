<?php
interface CloudInterface{
	// refresh folder from dropbox is not part of interface because it is an internal function and 
	// not called directly
		public function upload($userCloudID, $userfile, $cloudDestinationPath);
		public function download($userCloudID, $cloudSourcePath, $fileName);
		public function getFolderContents($userCloudID, $folderPath);
		public function createFolder($userCloudID, $folderPath);
		public function delete($userCloudID , $completePath);
		public function getRegistrationPage($userCloudName);
		public function getCompletion();
		public function downloadFolder($userCloudID, $folderPath);
}