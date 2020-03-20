<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Entity\User
 * @covers \App\Entity\Task
 */
class UserTest extends WebTestCase
{
	private $encoder;

	protected function setUp(): void
	{
		static::bootKernel();
		$container = self::$container;

		$this->encoder = $container->get('security.password_encoder');
	}
	/**
	 * Test id assign task
	 * 
	 * @return void
	 * 
	 */
	public function testId()
	{
		$user = new User();
		$id = null;

		$this->assertEquals($id, $user->getId());
	}

	/**
	 * Test field username
	 * 
	 * @return void
	 */
	public function testUsername()
	{
		$user = new User();
		$username = "Test username";

		$user->setUsername($username);
		$this->assertEquals($username, $user->getUsername());
	}

	/**
	 * Test encode password
	 * 
	 * @return void
	 */
	public function testEncodePassword()
	{
		$user = new User();
		$password = "test password";
		$user->setPassword($this->encoder->encodePassword($user, $password));
		$this->assertTrue($this->encoder->isPasswordValid($user, $password));
	}

	/**
	 * Test field password
	 * 
	 * @return void
	 */
	public function testPassword()
	{
		$user = new User();
		$password = "test password";
		$passwordEncode = $this->encoder->encodePassword($user, $password);
		$user->setPassword($passwordEncode);
		$this->assertEquals($passwordEncode, $user->getPassword());
	}

	/**
	 * Test field email
	 * 
	 * @return void
	 */
	public function testEmail()
	{
		$user = new User();
		$email = "root@root.fr";

		$user->setEmail($email);
		$this->assertEquals($email, $user->getEmail());
	}

	/**
	 * Test add task
	 * 
	 * @return void
	 */
	public function testAddTask()
	{
		$user = new User();
		$task = new Task();

		$user->addTask($task);
		$this->assertEquals($task, $user->getTasks()[0]);
	}

	/**
	 * Test remove task
	 * 
	 * @return void
	 */
	public function testRemoveTask()
	{
		$user = new User();
		$task = new Task();

		$user->addTask($task);
		$this->assertEquals($task, $user->getTasks()[0]);

		$user->removeTask($task);
		$this->assertEquals([], $user->getTasks()->toArray());
	}

	/**
	 * Test field role
	 * 
	 * @return void
	 */
	public function testRole()
	{
		$user = new User();
		$role = '["ROLE_ADMIN"]';

		$user->setRole($role);
		$this->assertEquals($role, $user->getRole());
	}
}
