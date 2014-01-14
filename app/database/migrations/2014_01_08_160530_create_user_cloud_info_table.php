<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserCloudInfoTable extends Migration {

	public function up()
	{
		Schema::create('user_cloud_info',function($table) {
			$table->integer('userID')->unsigned();
			$table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
			$table->integer('cloudID')->unsigned();
			$table->foreign('cloudID')->references('cloudID')->on('clouds')->onDelete('cascade');
			$table->text('access_token');
			$table->primary(array('userID','cloudID'));
			$table->timestamps();

		});	
	}

	
	public function down()
	{
		Schema::drop('user_cloud_info');
	}

}