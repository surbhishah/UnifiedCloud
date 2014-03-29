<?php
class GoogleDrive implements CloudInterface{
		

		public function upload($userCloudID, $userfile, $cloudDestinationPath){

			try{
				// Set the path to the directory where the temp files will be stored
				// We append the userCloudID of the user so that files of same name do not clash with each other
				$serverDestinationPath = public_path().'/temp/googledrive/uploads/';
				if(!is_dir($serverDestinationPath.$userCloudID)){
					mkdir($serverDestinationPath.$userCloudID);
				}

				$serverDestinationPath = public_path().'/temp/googledrive/uploads/'.$userCloudID.'/';
				
				// Get the file from the form
				$file = $userfile;

				// Get the name of the file of the user
				$fileName = $file->getClientOriginalName();
				
				// Store the file on the server
				$file->move($serverDestinationPath, $fileName);
				
				$mime=$file->getClientMimeType();

				$client= $this->getClientObject($userCloudID);
				
				
			$service = new Google_DriveService($client);
				$arr =  Utility::splitPath($cloudDestinationPath);
				$folderName=$arr[1];
				if($folderName<>''){
				$s="mimeType='application/vnd.google-apps.folder' and trashed=false and title contains '".$folderName."'";
			    $parameters = array("q"=> $s ,"maxResults"=>'1');
      
              $folderid=GoogleDrive::retrieveFolderId($service,$parameters);
          }
          else
          	$folderid='root';

               $serverDestinationPath= $serverDestinationPath.$fileName;
               $f =GoogleDrive::insertFile($service, $fileName, $folderid , $mime ,$serverDestinationPath);
               //return View::make('complete')->with('message',$f);

//               Log::info("f info received from googoe drive",array("fileID"=>$f['fileId']));
            
			   
			    $newFile = array();
				$newFile['path']=$cloudDestinationPath;

				$newFile['fileName']=$fileName;
				$newFile['lastModifiedTime']=$f['modifiedDate'];
					//echo $newFile['lastModifiedTime'];
				$newFile['rev']='rev';
				$newFile['size']=$f['quotaBytesUsed'];
				//echo $newFile['size'];
				//echo $arr['fileSize'];
				$newFile['isDirectory']='false';
				$newFile['hash']=null;// Passing null because we dont have hash values for these 
				// but we might get them in the future if $file is actually a folder 
				FileModel::addOrUpdateFile($userCloudID, $newFile);

			//view::send($token);
				
          }catch(Exception $e){
				Log::info("Exception raised in googledrive::upload");
				Log::error($e);
				throw $e;
			}
			
		}

		public function download($userCloudID, $cloudSourcePath, $fileName)
		{
			    $serverDownloadPath = public_path().'/temp/googledrive/downloads/';
			   
			    $file = FileModel::getFileAttributes($userCloudID, $cloudSourcePath, $fileName, array('fileID','rev'));

			if($file == null)
			{// no such file exists in our database
				throw new Exception('File not found in Dropbox::download',array('userCloudID'=>$userCloudID, 
						'cloudSourcePath'=>$cloudSourcePath, 'fileName'=>$fileName));
			}
			$fileID = $file->fileID;
			 $client= $this->getClientObject($userCloudID);
		 	$service = new Google_DriveService($client);
		
		
			/*if(Temp::TempFileExists($fileID)){
				 $fileDestination = $serverDownloadPath.$fileID;
				 return $fileDestination;

			}*/
			    $s="title contains '".$fileName."'";
      
       			$parameters = array("q"=> $s,"maxResults"=>1);
         		$fileid=GoogleDrive::retrieveFolderId($service,$parameters);

				$file_info=GoogleDrive::returnMimetype($service,$fileid);
		
                $flag= GoogleDrive::downloadfile($service,$file_info['mime'],$file_info['url'],$file_info['title'],$serverDownloadPath);
            //echo $file_info['mime'];
                //echo $serverDownloadPath.$file_info['title'];
                	return $serverDownloadPath.$file_info['title'];
                
		}

		public function getFolderContents($userCloudID, $folderPath, $cached=false)
		{
			
			try{
			 $key = $userCloudID.$folderPath;
			 if(Cache::has($key) && $cached =='true'){ //cached is a string, not boolean
			 	return Cache::get($key);
			 }
			  else {

			$client= $this->getClientObject($userCloudID);
			$service = new Google_DriveService($client);

			$f=Utility::splitPath($folderPath);
			$fileName = $f[1];
			if($fileName!='')
			{

			$s="mimeType='application/vnd.google-apps.folder' and trashed=false and title contains '".$fileName."'";
			 $parameters = array("q"=> $s ,"maxResults"=>'1');
			$folderid=self::retrieveFolderId($service, $parameters);
			$flag='true';
		 	}
		else
		{
			$folderid='root';

		

		
		}

	$flag =self::retrieveAllChanges($service, $startChangeId = NULL,$userCloudID,$folderPath,$folderid,$fileName);
	
		if($flag=='true')
			{
				
				
				
				$arr=self::printFilesInFolder($service,$folderid); //it returns fileid of each child file/folder of the
				//print_r($arr);
				//given forlder

				$meta=self::getMetaData($service,$arr);//returns metadata of each file in folder
				//print_r($meta);


							foreach ($meta as $m) {
					$filearr['fileName']=$m['title'];
					
					$filearr['path']=$folderPath;

					$filearr['rev']='rev';
					$filearr['lastModifiedTime']=$m['modifiedDate'];
					$filearr['isDirectory'] = $m['isDirectory'];
					
					$filearr['size']=$m['size'];
					$filearr['hash']=null;

				

				FileModel::addOrUpdateFile($userCloudID, $filearr);
 
				
				}

				return FileModel::getFolderContents($userCloudID,$folderPath);
			}
			else
			{
				
				/*$arr=self::printFilesInFolder($service,$folderid);
				$i=0;
				
				foreach($arr as $id)
				{
					$re=self::returnMimetype($service, $id);
					//print_r($re);
					$title=$re['title'];
					$path= $folderPath.'/'.$title;
				print_r($re);
					echo "<br><br> kamini";
					$folderContents[$i]=FileModel::getFolderContents($userCloudID,$path);
					
					$i=$i+1;
				}*/
				$folderContents=FileModel::getFolderContents($userCloudID,$folderPath);
			return $folderContents;
			}
	
			}}catch(Exception $e){
			Log::info("Exception raised in googledrive::getFolderContents",array('userCloudID'=>$userCloudID, 'folderPath'=>$folderPath));
			Log::error($e);				
			throw $e;
		}
		}
		
/**********************************************************************************
@params: $service: service object
		$folderid:id of folder/file whose changes is to be checked
		$path: path of file stored in database=path on cloud.
	@filName: name of folder/file whose changes is to be checked
		
 @action: fetches changes

@return: string ...true if changes occured else false
*********************************************************************************/

function retrieveAllChanges($service, $startChangeId = NULL,$userCloudID,$path,$folderid,$fileName) 
{
	  $result = array();
	  $startChangeId="2800";
  		$pageToken = NULL;
         try 
    	{
      		$parameters = array();
      		if ($startChangeId) 
      		{
        		$parameters['startChangeId'] = $startChangeId;
      		}
      		if ($pageToken) 
      		{
        		$parameters['pageToken'] = $pageToken;
      		}

		      $changes = $service->changes->listChanges($parameters);
			  $c=$changes['items'];
			  
		
			foreach($c as $d){
				
				if($folderid==$d['fileId'])
				{

					$new_date =$d['modificationDate'];
 					list($date,$t)=explode("T", $new_date);
					list($time,$p)=explode(".",$t);
					$new_date = $date.' '.$time;		
					$old_date =FileModel::getFileAttributes($userCloudID, $path, $fileName,array('last_modified_time'));
					
					if($old_date< $new_date || is_null($old_date))
					{
						return 'true';
					}

				}
								
 
     
    }} catch (Exception $e) {
      print "An error occurred: " . $e->getMessage();
     
    }
  return('false');

  	
}
/**********************************************************************************
@params: $service: service object
		$folderid:id of folder whose children are required
		
 @action: fetches id of children

@return: array containing ids
*********************************************************************************/


	private function printFilesInFolder($service, $folderId) 
	{
  		$pageToken = NULL;
		$i=0;
		$arr = array();


  		do 
  		{
    		try 
    		{
		      $parameters = array();
      		  if ($pageToken) 
      		  {
        		$parameters['pageToken'] = $pageToken;
      		  }
      			$children = $service->children->listChildren($folderId, $parameters);
	  
				foreach($children['items'] as $child)
				{ 
					$arr[$i]= $child['id'];	
					$i=$i+1;
				}

      //$pageToken = $children->getNextPageToken();
	   		$pageToken = NULL;
    		}
    		catch (Exception $e) 
    		{
		      print "An error occurred: " . $e->getMessage();
      			$pageToken = NULL;
    		}
  		} while ($pageToken);

	return($arr); 			

		}
	
/**********************************************************************************
@params: $service: service object
		$fileid:id of folder/file whose metadata is to be generated
		
 @action: fetched metadata

@return: array containing modifiedDate,size,title,isDirectory
*********************************************************************************/

	private function getMetaData($service,$fileid)
	{
      //print_r($fileid);
  		try 
  		{
  			$i=0;
  			$arr = Array();
  			$metadata=Array();
  			
				$arr[$i]=Array();
				$a=array('fields'=>'title,modifiedDate,quotaBytesUsed,mimeType');
				foreach($fileid as $c)
				{
					$f[$i] = $service->files->get($c,$a);
					$i=$i+1;				
				}


			$i=0;
			foreach ($f as $file) 
			{
	
					$new_date = $file['modifiedDate'];
 					list($date,$t)=explode("T", $new_date);
					list($time,$p)=explode(".",$t);
					$new_date = $date.' '.$time;

				$metadata['modifiedDate']=$new_date;
				$metadata['size']= $file['quotaBytesUsed'];
				$metadata['title']= $file['title'];
				if($file['mimeType']=='application/vnd.google-apps.folder')
				$metadata['isDirectory'] = true;
				else
				$metadata['isDirectory'] = false;
				$arr[$i]=$metadata;
				$i=$i+1;
			}


	 
			
	}
	catch(Exception $e){ //echo "An error occured : " . $e->getMessage();
	}
return($arr);
}
/*********************************************************************************
	@params: $folderpath: path to folder including folder name
	@action: Create an  empty folder on cloud

*********************************************************************************/
		public function createFolder($userCloudID, $folderPath){

				$client= $this->getClientObject($userCloudID);
			    $service = new Google_DriveService($client);
			    $mime = 'application/vnd.google-apps.folder';
			    $r=Utility::splitPath($folderPath);
			    $foldername=$r[1];
			    //$c=Utility::splitPath($r[0]);
			   $folderPath=substr($r[0],0,-1);
			   $r=Utility::splitPath($folderPath);
			    $parent_folder = $r[1];
				if($parent_folder!='')
				{

				$s="mimeType='application/vnd.google-apps.folder' and trashed=false and title contains '".$parent_folder."'";
			    $parameters = array("q"=> $s ,"maxResults"=>'1');
				$folderid=GoogleDrive::retrieveFolderId($service,$parameters);
				}			    
				else
					$folderid='root';
			    
              
               
               $f =GoogleDrive::insertFolder($service, $foldername, $folderid , $mime);

               $folderData = array();
			$folderData['path']=$folderPath;
			$folderData['fileName']=$foldername;
			$folderData['lastModifiedTime']=$f['modifiedDate']; 
			$folderData['rev']='rev';
			$folderData['isDirectory']=true;
			$folderData['size']= '0';
			$folderData['hash']=null;
			FileModel::addOrUpdateFile($userCloudID, $folderData);	
			return $folderData;

		}
	

		private function insertFolder($service, $title, $parentId, $mimeType) 
		{
  			$file = new Google_DriveFile();
  			$file->setTitle($title);
  			
  			$file->setMimeType($mimeType);

  			// Set the parent folder.
  			if ($parentId != null) 
  			{
    			$parent = new Google_ParentReference();
    			$parent->setId($parentId);
    			$file->setParents(array($parent));
  			}	

  			try 
  			{
    			$createdFile = $service->files->insert($file, array(
      			'data'=>'',
      			'mimeType' => $mimeType,
    		));

    // Uncomment the following line to print the File ID
    // print 'File ID: %s' % $createdFile->getId();

    		return $createdFile;
  		} catch (Exception $e) 
  		  {
    		print "An error occurred: in insert" . $e->getMessage();
    		

  		  }


		}
/********************************************************************************	
@params: $completePath contains complete path to cloud including filename 
		$userCloudID: id of user cloud given by server

	
*********************************************************************************/
		public function delete($userCloudID , $completePath){
			
			$client= $this->getClientObject($userCloudID);
			$service = new Google_DriveService($client);
            
			$arr =  Utility::splitPath($completePath);
			$filename=$arr[1];
			$s="title contains '".$filename."'";
		    $parameters = array("q"=> $s,"maxResults"=>1);

            $fileid=GoogleDrive::retrieveFolderId($service,$parameters);
            if(!is_null($fileid))
            {

            	GoogleDrive::deleteFile($service, $fileid);
            	FileModel::deleteFile($userCloudID, $arr[0], $filename);

            }
        	else
        	{
          		//echo "sorry file not found....try again!";

        	}

		}
		public function getRegistrationPage($userCloudName='googledrive'){
			try
			{		
				self::getAuth($userCloudName);

			}catch(Exception $e){
				Log::info("Exception raised in Dropbox::getRegistrationPage");
				Log::error($e);
				throw $e;
		}

		}
		public function getCompletion(){
            
			list($accessToken) = $this->getAuth()->finish($_GET);
			//echo $accessToken;
			$client = getClientObject($accessToken);
			$oauth2 = new Google_Oauth2Service($client);
			$userInfo = $oauth2->userinfo->get();
            $uid = $userInfo['id'];
	   	    $userID = Session::get('userID');
	   	    $userCloudName=Session::get('userCloudName');

   				if(User::userAlreadyExists($uid, self::$cloudID)){
					return Redirect::route('dashboard')
							->with('message','You already have an account with us!');
				}
				else if(UserCloudInfo::userCloudNameAlreadyExists($userID,self::$cloudID, $userCloudName)){
					return Redirect::route('dashboard')
							->with('message','You already have an account with this name "'.$userCloudName);		
				}
				else{
					$userCloudID = UserCloudInfo::setAccessToken($userID,$userCloudName, $uid, self::$cloudID,$token);
					return Redirect::route('dashboard')
							->with('message','Cloud successfully added "'.$userCloudName);		
				}


		}

public function getFullFileStructure($userCloudID){
			$client= $this->getClientObject($userCloudID);
			$service = new Google_DriveService($client);
			$folderid="root";
			$arr=self::printFilesInFolder($service,$folderid); //it returns fileid of each child file/folder of the
				
				//given forlder

			$meta=self::getMetaData($service,$arr);//returns metadata of each file in folder
			foreach($meta as $m){

					$newFile = array();
					$newFile['path']='/'.$m['title'];
					$newFile['fileName']=$m['title'];
					$newFile['lastModifiedTime']=$m['modifiedDate'];
					$newFile['rev']='rev';
					$newFile['size']=$m['size'];
					$newFile['isDirectory']=$m['is_dir'];
					$newFile['hash']=null;
					FileModel::addOrUpdateFile($userCloudID, $newFile);
			
			}
					

}
/**********************************************************************************
@params: $userCloudID: usercloudid
		$path:path where file is to be downloaded on server
 @action: downloades folder

@return: zipped file
*********************************************************************************/

		public function downloadFolder($userCloudID, $folderPath)
		{

			$serverDownloadPath = public_path().'/temp/googledrive/download/';
			$client= $this->getClientObject($userCloudID);
			$service = new Google_DriveService($client);
			$serverDownloadPath = public_path().'/temp/googledrive/download/';
			$f=Utility::splitPath($folderPath);
			$folderName=$f[1];
			if($folderName=='')
			{
				$s="mimeType='application/vnd.google\-apps.folder' and trashed=false and title contains '".$folderName."'";
		    	$parameters = array("q"=> $s ,"maxResults"=>'1');
				$folderid=self::retrieveFolderId($service,$parameters);
			}
			else
			$folderid='root';
			
			$flag=self::getFolder($service,$folderid,$folderName,$serverDownloadPath);
			if($flag=='true'){
			$jsonFilePath = $serverDownloadPath.$folderid.'.json';

			$array =self::getFolderContents($userCloudID,$folderPath);
				File::put($jsonFilePath,json_encode($array));
				return Utility::createZip($jsonFilePath,'googledrive');
			}
			else
			{

				//echo "error in downloading...!!";
			}
        }
/**********************************************************************************
@params: $service: service object
		$folderid:id of folder to be downloaded
		$folderName: name of folder to be downloaded
		$path:path where file is to be downloaded on server
 @action: recursively copies file on server in a particular folder

@return: string..true if successful download
*********************************************************************************/
    private function getFolder($service,$folderid,$folderName,$path)
	{

  		$arr = self::printFilesInFolder($service, $folderid);
  
   		mkdir($path.$folderName, 0700, 'true');
   		$path = $path.$folderName.'/';
 		foreach($arr as $c)
 		{

 			$d=self::returnMimetype($service,$c);
			if($d['mime']<>'application/vnd.google-apps.folder')
			{
				
			 	self::downloadFile($service,$d['mime'],$d['url'],$d['title'],$path);
				//echo "after";
 
            }else{
            	//echo "calling download file";
            	self::getFolder($service,$c,$d['title'],$path);}
        }
        return 'true';
    }
  

		

		private function getAuth($userCloudName){
			session_start();
			
			$client = new Google_Client();
			
			$client->setClientID('384532781768-98jkqnb5683qb72fkhvbs1kmasqrjp4e.apps.googleusercontent.com');
			$client->setClientSecret('qpcmArT7UhEHy46ibMs51WFS');
			$client->setDeveloperKey('AIzaSyCZFa9xF56smL3Dwx1vMCwEuTTgL5vo6q0s');
			$client->setAccessType('offline');
			//$client->setApprovalPrompt('force');
			$client->setScopes(array('https://www.googleapis.com/auth/userinfo.profile',
                    'https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/drive','https://www.googleapis.com/auth/drive.readonly.metadata','https://www.googleapis.com/auth/drive.appdata','https://www.googleapis.com/auth/drive.file','https://www.googleapis.com/auth/drive.readonly'));
			$redirectUri = "http://localhost/UnifiedCloud/public/auth/googledrive";
			$client->setRedirectUri($redirectUri);
			$service = new Google_DriveService($client);
            $oauth2 = new Google_Oauth2Service($client);
           // Session::put('userCloudName', $userCloudName );
			
			$ret= $client->authenticate();

			
			//$t=$client->getAccessToken($client);
//print_r($t);
			
            
		}
/******************************************************************************
	@params:$parentid: id of parent folder in which file is to be inserted
	 @action: insert file on drive
	 returns: void
	 *******************************************************************************/
		
		private function insertFile($service, $title, $parentId, $mimeType, $filename) 
		{
  			$file = new Google_DriveFile();
  			$file->setTitle($title);
  			$file->setDescription('');
  			$file->setMimeType($mimeType);

  			// Set the parent folder.
  			if ($parentId != null) 
  			{
    			$parent = new Google_ParentReference();
    			$parent->setId($parentId);
    			$file->setParents(array($parent));
  			}	

  			try 
  			{
    			$data = file_get_contents($filename);
				$createdFile = $service->files->insert($file, array(
      			'data' => $data,
      			'mimeType' => $mimeType,
    		));

    
    		return $createdFile;
  		} catch (Exception $e) 
  		  {
    		print "An error occurred: in insert" . $e->getMessage();
    
  		  }
    }
 /***************************************************************************************
	argument:usercloud id
	it also brings a new access token whenevr old expires 
	returns: client object. 
 ***************************************************************************************/
	private function getClientObject($userCloudID)
	{
  		$client = new Google_Client();
  		//$token = DB::table('user_cloud_info')->where('user_cloudID', $id)->pluck('access_token');
  		 //$token= json_encode($token); 

		// $path =app_path().'/database/googledrive-app-info.json';

  //       $json_data = file_get_contents($path);
		// $data_array = json_decode($json_data, true);
        $client = new Google_Client();
		$client->setAccessType('ofline'); // default: offline

		$client->setClientId('384532781768-98jkqnb5683qb72fkhvbs1kmasqrjp4e.apps.googleusercontent.com');
		$client->setClientSecret('qpcmArT7UhEHy46ibMs51WFS');
		
		//$client->setRedirectUri($scriptUri);

		$client->setScopes(array('https://www.googleapis.com/auth/userinfo.profile',
    'https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/drive','https://www.googleapis.com/auth/drive.readonly.metadata','https://www.googleapis.com/auth/drive.appdata','https://www.googleapis.com/auth/drive.file','https://www.googleapis.com/auth/drive.readonly'));


		$oauth2 = new Google_Oauth2Service($client);
		$service = new Google_DriveService($client);
		
		//$client->getAccessToken($client); 
		$arr= array('access_token' => 'ya29.1.AADtN_Xx8mnEnzInlozLZ7oquyhYo5JFm__ShXo7wksbnAICtpDfRiGMt97IREhO',
			'token_type' => 'Bearer', 'expires_in' => 3600, 
			'id_token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjkyZDkxNzNiYjgxNjA5NjNlNjRhZDUzYzEzYTFkMmEzOWE3ZWUyNGMifQ.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiZW1haWwiOiJnYXJnLnBvb2phMjIwNjkyQGdtYWlsLmNvbSIsImlkIjoiMTAxODY1ODAzMDA1MTY1ODczOTI3Iiwic3ViIjoiMTAxODY1ODAzMDA1MTY1ODczOTI3IiwiY2lkIjoiMzg0NTMyNzgxNzY4LTk4amtxbmI1NjgzcWI3MmZraHZiczFrbWFzcXJqcDRlLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXpwIjoiMzg0NTMyNzgxNzY4LTk4amtxbmI1NjgzcWI3MmZraHZiczFrbWFzcXJqcDRlLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwidG9rZW5faGFzaCI6InpMbEVqQWhBdW10bHVYU285TV8xbUEiLCJhdF9oYXNoIjoiekxsRWpBaEF1bXRsdVhTbzlNXzFtQSIsInZlcmlmaWVkX2VtYWlsIjoidHJ1ZSIsImVtYWlsX3ZlcmlmaWVkIjoidHJ1ZSIsImF1ZCI6IjM4NDUzMjc4MTc2OC05OGprcW5iNTY4M3FiNzJma2h2YnMxa21hc3FyanA0ZS5hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbSIsImlhdCI6MTM5NTkxMjg2MiwiZXhwIjoxMzk1OTE2NzYyfQ.BvISjGvX2DhFUm6F1K6ITm3lINeovw8LzaKWzr1X5LXCxGMXyVgF-LhIx-5zXZgYYnnuI2wAVYcWLXaVKML2GaAys-op1btbwSJ8medZGzZNLnXLTV_GUxPVIwa5ZC648_G3jcufb4GzlUryV1MqEwad6BIW80vNFUMCceysrws',
			 'refresh_token' => '1/lypt_tQ8VtvtOTO_8w4kLjZFpnoctdOeYysQpWSYpyw','created' => 1395913155);
		$token = json_encode($arr);
		$client->setAccessToken($token);
	

        if($client->isAccessTokenExpired()) 
        {
    		//$client->refreshToken(' 1/5pjNXv3Pj3c00aZETO-38ZZImKNXo1uZXOvHkepkNqg');
    		
    		$r=$client->revokeToken($token);
    		
    		$token =$client->getAccessToken();
 			$client->setAccessToken($token);
 	
		

			if($client->isAccessTokenExpired()){}
			


		}
        return $client;
  
  } //end of getClientObject Function

  	/******************************************************************************
	  
	 argument:service object and parameters which has query with file/folder name
	 used for finding id of folder/file
	 returns: id(string)
	 *******************************************************************************/
  private function retrieveFolderId($service,$parameters) {
  
   $result = array();
   $pageToken = null;
   $DriveFile = new Google_DriveFile();
   $r=null;
 
   
     try
     {
        if ($pageToken) 
       {
         $parameters['pageToken'] = $pageToken;
       } 

        $files = $service->files->listFiles($parameters);
    	$result = $files['items'];
      	$result = $result[0];
     	$required_id= $result['id'];
     	return($required_id);
 	}catch(Exception $e){//echo "Error occured: " .$e->getMessage();
 		}
  		
      
	}
	/******************************************************************************
	 argument:service object and id of file to be deleted
	 used for deleting file
	 returns: void
	 *******************************************************************************/
	private function deleteFile($service,$fileId)
	{
		try {
    				$service->files->delete($fileId);
    				
  				} catch (Exception $e)
  				{
    				print "An error occurred: " . $e->getMessage();
  				}

	}
	/******************************************************************************
	 argument:service object and file id 
	 used in download folder for finding mimetype of file to be downloaded and url 
	 returns: array containing mimetype title
	 *******************************************************************************/
	
	private function returnMimetype($service, $fileId) 
	{
  		try 
  		{
    		$file = $service->files->get($fileId);
    		if($file['mimeType']<>'application/vnd.google-apps.folder')
			{
				$url = $file['downloadUrl'];
			}
			else
			{
				$url = null;
			}
			Log::info("return MIMetype ", array("quotaBytesUsed" => $file['quotaBytesUsed'] ));
	        $arr =  array('mime'=> $file['mimeType'],'url' => $url , 'title'=> $file['title'],'modifiedDate'=>$file['modifiedDate'],'fileSize'=>$file['quotaBytesUsed']);

            return($arr);
 
        } catch (Exception $e) 
        {
            print "An error occurred: " . $e->getMessage();
        }
	}
	private function downloadFile($service,$mime,$url,$fileName,$path) 
	{

        if ($url) 
        {
    		$request = new Google_HttpRequest($url, 'GET', null, null);
    		$httpRequest = Google_Client::$io->authenticatedRequest($request);
    		if ($httpRequest->getResponseHttpCode() == 200) 
    		{
				file_put_contents($path . $fileName , $httpRequest->getResponseBody());
	  			return 1;
	        } 
	        else 
	        {
	         	return 0;
    		}
  		} 
  		else 
  		{
    		return 0;
  	    }
    }
}