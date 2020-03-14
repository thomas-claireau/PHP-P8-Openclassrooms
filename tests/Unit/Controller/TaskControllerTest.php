<?php

namespace App\Tests\Unit\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Unit\Controller\SecurityControllerTest;

class TaskControllerTest extends WebTestCase
{
	private $client;
	private $securityControllerTest;

	public function setUp(): void
	{
		$this->client = static::createClient();
		$this->securityControllerTest = new SecurityControllerTest;
	}
	/**
	 * Test access tasks list
	 * 
	 * @return void
	 */
	// public function testAccessTaskList()
	// {
	// 	$this->securityControllerTest->logIn('admin');
	// 	// $this->client->request('GET', SecurityControllerTest::ROUTE_TASK);
	// 	$this->assertEquals('200', $this->client->getResponse()->getStatusCode());
	// }

	/**
	 * Test create task
	 * 
	 * @return void
	 */
	// public function testCreateTask()
	// {
	// }

	/**
	 * Test update task
	 * 
	 * @return void
	 */
	// public function testUpdateTask()
	// {
	// }

	/**
	 * Test toggle task
	 * 
	 * @return void
	 */
	// public function testToggleTask()
	// {
	// }

	/**
	 * Test remove task by unauthorized
	 * 
	 * @return void
	 */
	// public function testRemoveTaskByUnauthorized()
	// {
	// }

	/**
	 * Test remove task by authorized
	 * 
	 * @return void
	 */
	// public function testRemoveTaskByAuthorized()
	// {
	// }

	/**
	 * Test remove anonymous task by admin
	 * 
	 * @return void
	 */
	// public function testRemoveAnonymousTaskByAdmin()
	// {
	// }
}
