<?php

# app/repositories/UserRepositoryInterface.php

interface UserRepositoryInterface {
	
	public function createUser($firstName,$lastName,$email,$password);

	public function getUserAttributes($email,$attributes);

	
}