<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	public function up()
	{
			Schema::create('users',function($table) {
			$table->increments('userID');
			$table->string('email')->unique();
			$table->string('first_name');
			$table->string('last_name');
			$table->text('password');
			$table->text('encrypted_key');
			$table->timestamps();
		});
	}


	public function down()
	{
		Schema::drop('users');
	}

}
