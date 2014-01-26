<?php

use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {

	public function up()
	{
		Schema::create('files',function($table) {
			$table->increments('fileID');
			$table->string('file_name'); // Name of the file or folder 

			$table->integer('user_cloudID')->unsigned();
			$table->foreign('user_cloudID')->references('user_cloudID')->on('user_cloud_info')->onDelete('cascade');
			

			// Path not including the filename
			// This path is the path of the file in the Cloud 
			$table->string('path');
			$table->boolean('is_encrypted');
			$table->boolean('is_directory');// ADDED LATER 
			$table->timestamp('last_modified_time');
			$table->integer('size');
			$table->string('rev');	// ADDED LATER This field is a unique identifier for the current version of a file 
									// This field is also the HASH of a folder 
									// In dropbox , documentation says that hash of a folder is rev equivalent
			$table->timestamps();
			$table->unique(array('user_cloudID','file_name','path'));
			$table->string('hash')->nullable();	// is nullable because files dont have hashes in dropbox 
		});		
	}

	public function down()
	{
		Schema::drop('files');
	}

}