<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration {

	public function up()
	{
		Schema::create('groups',function($table) {
			$table->increments('groupID');
			$table->string('name');
			$table->integer('adminID')->unsigned();
			$table->foreign('adminID')->references('userID')->on('users')->onDelete('cascade');
			$table->unique(array('name','adminID'));// a user(admin) cannot have two groups of same name
			// However, user can belong to multiple groups of same name
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('groups');
	}

}
