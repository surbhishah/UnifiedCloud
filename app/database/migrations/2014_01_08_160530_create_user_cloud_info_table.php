<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserCloudInfoTable extends Migration {

	public function up()
	{
		Schema::create('user_cloud_info',function($table) {
			$table->increments('user_cloud_info_id');
			
			$table->integer('userID')->unsigned();
			$table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
			$table->integer('cloudID')->unsigned();
			$table->foreign('cloudID')->references('cloudID')->on('clouds')->onDelete('cascade');
			$table->text('access_token');
			$table->boolean('has_user_files');
			//cursor :A string that encodes the latest information that has been returned. 
			$table->text('cursor');
			$table->unique(array('userID','cloudID'));
			//$table ->primary('user_cloud_info_id');// Eloquent does not support composite primary keys 
								//Hence, I am making a dummy key and also have made the combination of userID
								// and cloudID unique
								// I dont think that will make any difference ..
								// Please NOTE	
			$table->timestamps();

		});	
	}

	
	public function down()
	{
		Schema::drop('user_cloud_info');
	}

}