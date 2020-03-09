<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;


class TaskTest extends TestCase
{
	/**
	 * Test id assign task
	 * 
	 * @return void
	 * 
	 */
	public function testId()
	{
		$task = new Task();
		$id = null;

		$this->assertEquals($id, $task->getId());
	}

	/**
	 * Test field created_at
	 * 
	 * @return void
	 */
	public function testCreatedAt()
	{
		$task = new Task();
		$date = new \DateTime();
		$createdAt = $date;

		$task->setCreatedAt($createdAt);
		$this->assertEquals($date, $task->getCreatedAt());
	}

	/**
	 * Test field title
	 * 
	 * @return void
	 */
	public function testTitle()
	{
		$task = new Task();
		$title = "Test titre";

		$task->setTitle($title);
		$this->assertEquals($title, $task->getTitle());
	}

	/**
	 * Test field content
	 * 
	 * @return void
	 */
	public function testContent()
	{
		$task = new Task();
		$content = "Test content";

		$task->setContent($content);
		$this->assertEquals($content, $task->getContent());
	}

	/**
	 * Test field isDone
	 * 
	 * @return void
	 */
	public function testIsDone()
	{
		$task = new Task();
		$isDone = true;

		$task->toggle($isDone);
		$this->assertEquals($isDone, $task->isDone());
	}

	/**
	 * Test field updated_at
	 * 
	 * @return void
	 */
	public function testUpdatedAt()
	{
		$task = new Task();
		$date = new \DateTime();
		$updatedAt = $date;

		$task->setUpdatedAt($updatedAt);
		$this->assertEquals($date, $task->getUpdatedAt());
	}

	/**
	 * Test field user
	 * 
	 * @return void
	 */
	public function testUser()
	{
		$task = new Task();
		$user = new User();

		$task->setUser($user);
		$this->assertEquals($user, $task->getUser());
	}
}
