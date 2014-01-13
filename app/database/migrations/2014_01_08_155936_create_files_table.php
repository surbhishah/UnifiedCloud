<?php

use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {

	public function up()
	{
		Schema::create('files',function($table) {
			$table->increments('fileID');
			$table->text('file_name'); // Name of the file or folder 

			// User ID of the user to which a file belongs
			$table->integer('userID')->unsigned();
			$table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
			
			// Cloud ID of the cloud to which a file belongs
			$table->integer('cloudID')->unsigned();
			$table->foreign('cloudID')->references('cloudID')->on('clouds')->onDelete('cascade');

			// Path not including the filename
			// This path is the path of the file in the Cloud 
			$table->text('path');
			$table->boolean('is_encrypted');
			$table->boolean('is_directory');// ADDED LATER 
			$table->timestamp('last_modified_time');
			$table->integer('size');
			$table->string('rev');	// ADDED LATER This field is a unique identifier for the current version of a file 
									// This field is also the HASH of a folder 
									// In dropbox , documentation says that hash of a folder is rev equivalent
			$table->timestamps();
			
		});		
	}

	public function down()
	{
		Schema::drop('files');
	}

}