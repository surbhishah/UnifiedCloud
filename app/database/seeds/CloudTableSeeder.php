<?php
 
class CloudTableSeeder extends Seeder {
 
  public function run()
  {
    // Delete the contents of the table first
      DB::table('clouds')->delete();
    
    // Add new entries to the table 
      DB::table('clouds')->insert(array(
        "cloudID"=>"1",
        "name"=>"Dropbox",
        "app_key"=>"sa62ueedrflmeqz",
        "app_secret"=>"2sdzxwtyo1qdp25",
        "redirect_uri"=>"http://localhost/UnifiedCloud/auth/dropbox",        
//        "created_at"=>date('y-m-d h:m:s'),
  //      "updated_at"=>date('y-m-d h:m:s')
      ));
  }
 
}

  