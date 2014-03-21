<?php

class UserControllerTest extends TestCase {

	public function __construct() {
	
	}
	/**
	 * called before testing starts to set up whatever we want set up before testing.
	 */
	public function setUp() {
		parent::setUp();

		$this->mock = $this->mock('EloquentUserRepository');
	}

	/**
	 * function to bind mock class with whatever interface or class we wish to mock.
	 * @param  string $class class to be mocked
	 * @return Meockery Object        
	 * @group UsersController
	 */
	public function mock($class) {
		$mock = Mockery::mock($class);
		$this->app->instance($class,$mock);

		return $mock;
	}

	/**
	 * called after all tests have run.
	 */
	public function tearDown() {
		Mockery::close();
	}

	/**
	 * tests UsersController::postCreate for incorrect input, routing to sign_up route.
	 * @group UsersController
	 */
	public function testPostCreateForIncorrectSignUp() {

		Validator::shouldReceive('make')
			->once()
			->andReturn(Mockery::mock(['fails' => true]));

		$this->mock
		->shouldReceive('createUser')
		->never();

		$this->call('POST','user/signup');
		$this->assertRedirectedToRoute('sign_up');
	}

	/**
	 * tests UsersController::postCreate for correct input, routing to landing route.
	 * @group UsersController
	 */
	public function testPostCreateForCorrectSignUp() {
	
		Validator::shouldReceive('make')
		->once()
		->andReturn(Mockery::mock(['fails' => false]));

		$this->mock
		->shouldReceive('createUser')
		->once();

		$this->action('POST','UsersController@postCreate');
		$this->assertRedirectedToRoute('landing');
	}

	/**
	 * tests UsersController::postSign for correct input, routing to dashboard route
	 * @group UsersController
	 */
	public function testPostSigninCorrect() {
	
		Auth::shouldReceive('attempt')
				->once()
				->andReturn(true);

		$this->mock
		->shouldReceive('getUserAttributes')
		->once()
		->andReturnValues(array('userID' => 1));


		$this->action('POST','UsersController@postSignin');
		$this->assertRedirectedToRoute('dashboard');
	}

	/**
	 * tests UsersController::postSign for incorrect input, routing to dashboard route
	 * @group UsersController
	 */
	public function testPostSigninIncorrect() {
	
		Auth::shouldReceive('attempt')
				->once()
				->andReturn(false);

		$this->action('POST','UsersController@postSignin');
		$this->assertRedirectedToRoute('sign_in_page');
	}
}