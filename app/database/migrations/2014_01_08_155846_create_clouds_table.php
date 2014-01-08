<?php

use Illuminate\Database\Migrations\Migration;

class CreateCloudsTable extends Migration {

	public function up()
	{
		Schema::create('clouds',function($table) {
			$table->increments('cloudID');
			$table->string('name')->unique();
			$table->string('app_key');
			$table->text('app_secret');
			$table->text('redirect_uri');
			$table->timestamps();
			});	
	}

	
	public function down()
	{
		Schema::drop('clouds');
	}

}