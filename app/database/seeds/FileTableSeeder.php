<?php
 
class FileTableSeeder extends Seeder {
 
  public function run(){
 		DB::table('files')->delete();

		$faker = Faker\Factory::create();
		for ($i = 1; $i < 5; $i++)
		{
			DB::table('files')->insert(array(
					'fileID'=>$i,
					'file_name'=>$faker->word.$faker->fileExtension,
					'userID'=>'1',
					'cloudID'=>'1',
					'last_modified_time'=>date('y-m-d h:m:s'),
					'size'=>$faker->randomNumber(0,200000),
					'path'=>'/'.$faker->word.'/'.$faker->word,
//					'created_at'=> date('y-m-d h:m:s'),
//					'updated_at'=> date('y-m-d h:m:s')

				));
		}

		for ($i = 5; $i < 10; $i++)
		{
			DB::table('files')->insert(array(
					'fileID'=>$i,
					'file_name'=>$faker->word.$faker->fileExtension,
					'userID'=>'2',
					'cloudID'=>'1',	
					'last_modified_time'=>date('y-m-d h:m:s'),
					'size'=>$faker->randomNumber(0,200000),
					'path'=>'/'.$faker->word.'/'.$faker->word,
//					'created_at'=> date('y-m-d h:m:s'),
//					'updated_at'=> date('y-m-d h:m:s')

				));
		}

		for ($i = 10; $i < 15; $i++)
		{
			DB::table('files')->insert(array(
					'fileID'=>$i,
					'file_name'=>$faker->word.$faker->fileExtension,
					'userID'=>'3',
					'cloudID'=>'1',
					'last_modified_time'=>date('y-m-d h:m:s'),
					'size'=>$faker->randomNumber(0,200000),
					'path'=>'/'.$faker->word.'/'.$faker->word,
//					'created_at'=> date('y-m-d h:m:s'),
//					'updated_at'=> date('y-m-d h:m:s')

				));
		}
 		 
  }
 
}