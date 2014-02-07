<?php

class EloquentUserRepository implements UserRepositoryInterface {

	public function createUser($firstName,$lastName,$email,$password) {
		return User::createUser($firstName,$lastName,$email,$password);
	}

	public function getUserAttributes($email,$attributes) {
		return User::getUserAttributes($email,$attributes)->toArray();
	}

}