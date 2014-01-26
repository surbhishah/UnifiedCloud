<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class File extends Eloquent  {

/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
		protected $table = 'files';
		protected $nullable = 'hash';
	
}