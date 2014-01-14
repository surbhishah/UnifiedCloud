<?php

class UserTableSeeder extends Seeder {

  public function run(){
    // Delete the contents of the table first
      DB::table('users')->delete();

    // Add new entries to the table
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


      DB::table('users')->insert(array(
          'userID'=>'3',
          'email'=> 'jhalakjain23@gmail.com',
          'first_name'  =>  'jhalak',
          'last_name'   =>  'jain',
          'password' => Hash::make('password')
      ));

      DB::table('users')->insert(array(
          'userID'=>'4',
          'email'=> 'garg.pooja220692@gmail.com',
          'first_name'  =>  'pooja',
          'last_name'   =>  'garg',
          'password' => Hash::make('password')
      ));


  }

}
