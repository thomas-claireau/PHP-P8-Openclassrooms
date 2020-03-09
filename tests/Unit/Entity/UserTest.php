<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;


class UserTest extends TestCase
{
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
	 * Test field password
	 * 
	 * @return void
	 */
	public function testPassword()
	{
		$user = new User();
		$password = "test password";
	}

	/**
	 * Test field email
	 * 
	 * @return void
	 */
	public function testEmail()
	{
		$user = new User();
	}

	/**
	 * Test field tasks
	 * 
	 * @return void
	 */
	public function testTasks()
	{
		$user = new User();
	}

	/**
	 * Test field role
	 * 
	 * @return void
	 */
	public function testRole()
	{
		$user = new User();
	}
}
