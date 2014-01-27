<?php

use Illuminate\Database\Migrations\Migration;

class CreateSharedFilesTable extends Migration {

	public function up()
	{
			Schema::create('SharedFiles',function($table) {
			$table->integer('fileID')->unsigned();
			$table->foreign('fileID')->references('fileID')->on('files')->onDelete('cascade');
			$table->integer('ownerID')->references('userID')->on('users')->onDelete('cascade');
			$table->integer('sharerID')->references('userID')->on('users')->onDelete('cascade');
			$table->enum('access_rights', array('R', 'RW'));
			$table->timestamps();
		});
	}
	

	public function down()
	{
		Schema::drop('SharedFiles');
	}

}