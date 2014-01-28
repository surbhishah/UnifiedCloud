<?php


class UserCloudInfo extends Eloquent  {

	protected $table = 'user_cloud_info';
	protected $primaryKey='user_cloudID';
/**********************************************************************************************/
	/*
	*	@params:
	*		userID : ID of the user 
	*		userCloudName: name specified by user to name his cloud
	*		uid: unique id returned by dropbox, it uniquely identifies a user of dropbo
	*		cloudID: ID of the cloud
	*		accessToken: accessToken received from the cloud
	*	@return value:
	*	 	Boolean : userCloudID of the new cloud 
	*/

	public static function setAccessToken($userID,$userCloudName,$uid,$cloudID,$accessToken) {
		$userCloudInfo = new UserCloudInfo;
		$userCloudInfo->userID = $userID;
		$userCloudInfo->user_cloud_name=$userCloudName;
		$userCloudInfo->uid  = $uid;
		$userCloudInfo->cloudID = $cloudID;
		$userCloudInfo->access_token = $accessToken;
		$userCloudInfo->save();
		return $userCloudInfo->user_cloudID;
	}
	
/**********************************************************************************************/
	/*
	*	@params:
	*		userCloudID : ID of the user 's cloud
	*	@return value:
	*	 	string: access_token
	*	@decription : Returns access Token of a user 
	*
	*/
	public static function getAccessToken($userCloudID){
		return UserCloudInfo::where('user_cloudID','=',$userCloudID)->get()
									->first()->pluck('access_token');		
	}
/**********************************************************************************************/	
	/*
	*	@params:
	*		userID : ID of the user
	*	@return value:
	*	 	Boolean : Array of all clouds of the user
	*/
		
	public static function getClouds($userID) {
		// user_cloudID is also returned 
		$clouds = DB::table('user_cloud_info')
						->join('clouds',function($join) use ($userID){
							$join->on('user_cloud_info.cloudID','=','clouds.cloudID')
								->where('user_cloud_info.userID','=',$userID);
						})->select('clouds.name','user_cloud_info.user_cloudID','user_cloud_info.user_cloud_name')
						->orderBy('clouds.name')
						->get();
		return $clouds;
	}
/**********************************************************************************************/
	/*
	*	@params:
	*		userID: userID of the user
	*		cloudID: ID of the cloud
	*		userCloudName: Name assigned by user to his cloud
	*	@return value:
	*	 	Boolean : true if user already has cloud with that name and cloudProvider
	*					For eg: a user may have two clouds named "ABC" with dropbox and google Drive
	*					This is not a problem but he must not have two clouds named "ABC" on dropbox
	*				  false otherwise
	*/
		public static function userCloudNameAlreadyExists($userID,$cloudID, $userCloudName){
			$userCloudInfo = UserCloudInfo::where('userID','=',$userID)->where('cloudID','=',$cloudID)
							->where('user_cloud_name','=',$userCloudName)->get()->first();
			if($userCloudInfo == null)return false;
			else return true;
		}
}
/**********************************************************************************************/
	