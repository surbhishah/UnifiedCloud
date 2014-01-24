
<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserCloudInfoTable extends Migration {

	public function up()
	{
		Schema::create('user_cloud_info',function($table) {
			$table->increments('user_cloudID');
			$table->string('user_cloud_name');// email id of that particular cloud
			$table->string('uid');// uid of the user sent by cloud 
			$table->integer('cloudID')->unsigned();
			$table->foreign('cloudID')->references('cloudID')->on('clouds')->onDelete('cascade');
			

			$table->integer('userID')->unsigned();
			$table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
			
			$table->unique(array('userID','user_cloud_name','cloudID'));
			$table->unique(array('uid','cloudID'));
			$table->text('access_token');
			$table->boolean('has_user_files');
			$table->string('folder_hash');
			//cursor :A string that encodes the latest information that has been returned. 
			$table->text('cursor');
			$table->timestamps();

		});	
	}

	
	public function down()
	{
		Schema::drop('user_cloud_info');
	}

}