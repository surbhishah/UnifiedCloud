<?php
class GoogleDrive implements Cloud{
	public function upload($userID, $userfile, $cloudDestinationPath){}
	public function download($userID, $cloudSourcePath,$fileName){}
	public function getRegistrationPage(){}
	public function getCompletion(){}
	public function getFullFileStructure($userID){}
	public function refreshFullFileStructure($userID){}
	public function delete($userID,$path){}
	public function createFolder($userID,$folderPath){}
	public function getFolderContents($userID, $folderPath){}

}