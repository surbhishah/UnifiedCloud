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
          'email'=> 'surbhishah81@yahoo.in',
          'first_name'  =>  'surbhi',
          'last_name'   =>  'shah',
          'password' => Hash::make('password')
      ));
    DB::table('users')->insert(array(
          'userID'=>'2',
          'email'=> 'abhishek.alchemist@gmail.com',
          'first_name'  =>  'abhishek',
          'last_name'   =>  'nair',
          'password' => Hash::make('password')
      ));
    
      
      DB::table('clouds')->delete();    
      DB::table('clouds')->insert(array(
        "cloudID"=>"1",
        "name"=>"Dropbox",
        "app_key"=>"sa62ueedrflmeqz",
        "app_secret"=>"2sdzxwtyo1qdp25",
        "redirect_uri"=>"http://localhost/UnifiedCloud/auth/dropbox"
      ));
  
      DB::table('user_cloud_info')->delete();
      DB::table('user_cloud_info')->insert(array(
          'userID'=>'1',
          'user_cloudID'=>'1',
          'user_cloud_name'=>'surbhi_dropbox',
          'uid'=>'253426315',
          'cloudID'=>'1', 
          'access_token'=>'00fFM63sFIMAAAAAAAAAATvsZL-7Vmlmlijz6Bh6aUOE4FYB75f3W6aXxnxKs_tn'
        
        ));
      DB::table('user_cloud_info')->insert(array(
          'userID'=>'2',
          'user_cloudID'=>'2',
          'user_cloud_name'=>'abhishek_dropbox',
          'uid'=>'14162018',
          'cloudID'=>'1', 
          'access_token'=>'NiqYVYXZR9UAAAAAAAAAAcY5Qb6j0LO9IOowLnU13o6q-uNsTPMqjKb85B5N1LXQ'
        
        ));

      
  }
 
}