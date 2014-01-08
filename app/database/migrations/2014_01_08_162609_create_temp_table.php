<?php

use Illuminate\Database\Migrations\Migration;

class CreateTempTable extends Migration {

	public function up()
	{
		Schema::create('temp',function($table) {
			$table->integer('fileID')->unsigned();
			$table->foreign('fileID')->references('fileID')->on('files')->onDelete('cascade');
			$table->text('temp_file_name');
			$table->primary(array('fileID'));
			$table->timestamps();

		});
	}

	public function down()
	{
		Schema::drop('temp');
	}

}