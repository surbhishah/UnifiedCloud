<?php

class UserCloudInfoTableSeeder extends Seeder {


	public function run()
	{
		DB::table('user_cloud_info')->delete();
		DB::table('user_cloud_info')->insert(array(
        			'userID'=>'1',
					'cloudID'=>'1',	
					'access_token'=>''
			  
      	));

		DB::table('user_cloud_info')->insert(array(
        			'userID'=>'2',
					'cloudID'=>'1',	
					'access_token'=>''
			  
      	));

		DB::table('user_cloud_info')->insert(array(
        			'userID'=>'3',
					'cloudID'=>'1',	
					'access_token'=>''
			  
      	));


		DB::table('user_cloud_info')->insert(array(
        			'userID'=>'4',
					'cloudID'=>'1',	
					'access_token'=>''
			  
      	));


	}
		

}