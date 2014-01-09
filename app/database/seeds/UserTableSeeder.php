<?php
 
class UserTableSeeder extends Seeder {
 
  public function run(){
    // Delete the contents of the table first
      DB::table('users')->delete();

    // Add new entries to the table 
      DB::table('users')->insert(array(
          'userID'=>'1',
          'emailID'=> 'surbhishah81@yahoo.in',
          'first_name'  =>  'surbhi',
          'last_name'   =>  'shah',
          'password_hash' => Hash::make('password')
      ));

      DB::table('users')->insert(array(
          'userID'=>'2',
          'emailID'=> 'abhishek.alchemist@gmail.com',
          'first_name'  =>  'abhishek',
          'last_name'   =>  'nair',
          'password_hash' => Hash::make('password')
      ));
      

      DB::table('users')->insert(array(
          'userID'=>'3',
          'emailID'=> 'jhalakjain23@gmail.com',
          'first_name'  =>  'jhalak',
          'last_name'   =>  'jain',
          'password_hash' => Hash::make('password')
      ));
      
      DB::table('users')->insert(array(
          'userID'=>'4',
          'emailID'=> 'garg.pooja220692@gmail.com',
          'first_name'  =>  'pooja',
          'last_name'   =>  'garg',
          'password_hash' => Hash::make('password')
      ));


  }
 
}