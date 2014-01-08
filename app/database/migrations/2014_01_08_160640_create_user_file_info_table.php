<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserFileInfoTable extends Migration {

	public function up()
	{
		Schema::create('user_file_info_table',function($table) {
			$table->integer('userID')->unsigned();
			$table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
			$table->integer('fileID')->unsigned();
			$table->foreign('fileID')->references('fileID')->on('files')->onDelete('cascade');
			$table->primary(array('userID','fileID'));
			$table->timestamps();
		});	
	}

	public function down()
	{
		Schema::drop('user_file_info_table');
	}

}