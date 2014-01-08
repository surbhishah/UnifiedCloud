<?php

use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {

	public function up()
	{
		Schema::create('files',function($table) {
			$table->increments('fileID');
			$table->text('file_name');
			$table->text('path');
			$table->boolean('is_encrypted');
			$table->integer('cloudID')->unsigned();
			$table->foreign('cloudID')->references('cloudID')->on('clouds')->onDelete('cascade');
			$table->timestamp('last_modified_time');
			$table->integer('size');
			$table->timestamps();
		});		
	}

	public function down()
	{
		Schema::drop('files');
	}

}