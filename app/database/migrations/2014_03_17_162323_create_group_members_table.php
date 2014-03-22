<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMembersTable extends Migration {

	public function up()
	{
		Schema::create('group_members',function($table) {
			$table->increments('group_memberID');
			$table->integer('groupID')->unsigned();
			$table->foreign('groupID')->references('groupID')->on('groups')->onDelete('cascade');
			$table->integer('memberID')->unsigned();
			$table->foreign('memberID')->references('userID')->on('users')->onDelete('cascade');
			$table->unique(array('groupID','memberID'));
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('group_members');
	}

}
