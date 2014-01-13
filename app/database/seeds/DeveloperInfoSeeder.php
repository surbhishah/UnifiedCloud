<?php
 
class DeveloperInfoSeeder extends Seeder {
 
  public function run(){
    /** I am making this seeder so as to add information regarding myself and check 
     *  further functionality like fetching file information from dropbox 
     *  In case, you need to check something like that you need to add your own information
     *  Also, you also need to generate an access_token from Dropbox
     *  See the comments in routes.php 
     *  and put your access_token in place of mine
     *  ALso, I have commented the calls to other seeders in DatabaseSeeder.php class 
     *  Please check it out 
    */


      DB::table('users')->delete();
      DB::table('users')->insert(array(
          'userID'=>'1',
          'emailID'=> 'surbhishah81@yahoo.in',
          'first_name'  =>  'surbhi',
          'last_name'   =>  'shah',
          'password_hash' => Hash::make('password')
      ));
      
      DB::table('clouds')->delete();    
      DB::table('clouds')->insert(array(
        "cloudID"=>"1",
        "name"=>"Dropbox",
        "app_key"=>"sa62ueedrflmeqz",
        "app_secret"=>"2sdzxwtyo1qdp25",
        "redirect_uri"=>"http://localhost/UnifiedCloud/auth/dropbox",        
        "created_at"=>date('y-m-d h:m:s'),
        "updated_at"=>date('y-m-d h:m:s')
      ));
  
      DB::table('user_cloud_info')->delete();
      DB::table('user_cloud_info')->insert(array(
              'userID'=>'1',
          'cloudID'=>'1', 
          'access_token'=>'PIXXPZ9wS5oAAAAAAAAAAWjrWEud9FspD2Gpz3QRvbb2HSjV_Ga3rF8Okqz8bUfG'
        
        ));

      

  }
 
}