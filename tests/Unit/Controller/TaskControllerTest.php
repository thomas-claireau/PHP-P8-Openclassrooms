<?php

namespace App\Tests\Unit\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\LogUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\TaskController
 * @covers \App\Entity\User
 * @covers \App\Entity\Task
 */
class TaskControllerTest extends WebTestCase
{
	private $client;
	private $logUtils;
	private $idCreatedTask;
	private $entityManager;

	public function setUp(): void
	{
		$this->client = static::createClient();
		$this->logUtils = new LogUtils($this->client);

		$this->entityManager = $this->client->getContainer()
			->get('doctrine')
			->getManager();
	}
	/**
	 * Test access tasks list
	 * 
	 * @return void
	 */
	public function testAccessTaskList()
	{
		$this->logUtils->login('admin');
		$this->client->request('GET', "/tasks");
		$this->assertEquals('200', $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test create task
	 * 
	 * @return void
	 */
	public function testCreateTask()
	{
		$author = 'user';
		$date = new \DateTime();
		$date = $date->format('d/m/Y');
		$title = 'Test title';
		$content = 'Test content';

		$this->logUtils->login($author);
		$crawler = $this->client->request('GET', "/tasks/create");
		$crsf = $crawler->filter('input[name="task[_token]"]')->extract(array('value'))[0];

		$this->client->request('POST', "/tasks/create", [
			'task' => [
				'title' => $title,
				'content' => $content,
				'_token' => $crsf
			]
		]);

		// check if task is created
		$this->assertEquals(302, $this->client->getResponse()->getStatusCode());

		// check if task is present on the list of tasks
		$crawler = $this->client->followRedirect();

		$titleTask = $crawler->filter('.caption .portlet-header')->first()->text();
		$dateTask = $crawler->filter('.caption .inner .date')->first()->text();
		$authorTask = $crawler->filter('.caption .inner .author')->first()->text();
		$contentTask = $crawler->filter('.caption .inner .content')->first()->text();

		$this->setIdCreatedTask($crawler->filter('.task')->first()->attr('data-id'));

		// check if title task is present
		$this->assertStringContainsString($title, $titleTask);

		// check if date task is present
		$this->assertStringContainsString($date, $dateTask);

		// check if author task is present
		$this->assertStringContainsString($author, $authorTask);

		// check if content task is present
		$this->assertStringContainsString($content, $contentTask);
	}

	/**
	 * Test update task
	 * 
	 * @return void
	 */
	public function testUpdateTask()
	{
		$author = 'user';
		$date = new \DateTime();
		$date = $date->format('d/m/Y');
		$title = 'Test update title';
		$content = 'Test update content';

		$this->logUtils->login($author);
		$crawler = $this->client->request('GET', '/tasks');
		$link = $crawler->filter('.caption .link')->first()->attr('href');

		$crawler = $this->client->request('GET', $link);
		$crsf = $crawler->filter('input[name="task[_token]"]')->extract(array('value'))[0];

		$this->client->request('POST', $link, [
			'task' => [
				'title' => $title,
				'content' => $content,
				'_token' => $crsf
			]
		]);

		// check if task is created
		$this->assertEquals(302, $this->client->getResponse()->getStatusCode());

		// check if task is present and updated on the list of tasks
		$crawler = $this->client->followRedirect();

		$titleTask = $crawler->filter('.caption .portlet-header')->first()->text();
		$dateTask = $crawler->filter('.caption .inner .date')->first()->text();
		$authorTask = $crawler->filter('.caption .inner .author')->first()->text();
		$contentTask = $crawler->filter('.caption .inner .content')->first()->text();

		// check if title task is update
		$this->assertStringContainsString($title, $titleTask);

		// check if date task is update
		$this->assertStringContainsString($date, $dateTask);

		// check if author task is update
		$this->assertStringContainsString($author, $authorTask);

		// check if content task is update
		$this->assertStringContainsString($content, $contentTask);
	}

	/**
	 * Test toggle task
	 * 
	 * @return void
	 */
	public function testToggleTask()
	{
		$this->logUtils->login("admin");

		$task = $this->entityManager
			->getRepository(Task::class)
			->findOneBy([], ['id' => 'DESC']);

		$urlToggle = '/tasks/' . $task->getId() . '/toggle';
		$taskIsDoneBefore = $task->isDone();

		$this->client->request('POST', $urlToggle);
		$this->assertEquals(302, $this->client->getResponse()->getStatusCode());

		$taskIsDoneAfter = $task->isDone();

		$this->assertNotEquals($taskIsDoneBefore, $taskIsDoneAfter);
	}

	/**
	 * Test remove task by unauthorized
	 * 
	 * @return void
	 */
	public function testRemoveTaskByUnauthorized()
	{
		$this->testCreateTask();
		$idCreatedTask = $this->getIdCreatedTask();

		$taskCreated = $this->entityManager
			->getRepository(Task::class)
			->findOneBy(['id' => $idCreatedTask]);

		$urlDeleteTask = "/tasks/" . $idCreatedTask . "/delete";

		$this->logUtils->login("user2");

		$crawler = $this->client->request('POST', $urlDeleteTask);

		$this->assertEquals(302, $this->client->getResponse()->getStatusCode());

		$crawler = $this->client->followRedirect();
		$this->assertStringContainsString('Vous ne pouvez pas supprimer cette tâche', $crawler->text());

		$task = $this->entityManager
			->getRepository(Task::class)
			->findOneBy(['id' => $idCreatedTask]);

		$this->assertEquals($taskCreated, $task);
	}

	/**
	 * Test remove task by authorized
	 * 
	 * @return void
	 */
	public function testRemoveTaskByAuthorized()
	{
		$this->testCreateTask();
		$idCreatedTask = $this->getIdCreatedTask();

		$urlDeleteTask = "/tasks/" . $idCreatedTask . "/delete";

		$this->logUtils->login("user");

		$crawler = $this->client->request('POST', $urlDeleteTask);

		$this->assertEquals(302, $this->client->getResponse()->getStatusCode());

		$crawler = $this->client->followRedirect();
		$this->assertStringContainsString('La tâche a bien été supprimée', $crawler->text());

		$task = $this->entityManager
			->getRepository(Task::class)
			->findOneBy(['id' => $idCreatedTask]);

		$this->assertEquals(null, $task);
	}

	/**
	 * Test remove anonymous task by admin
	 * 
	 * @return void
	 */
	public function testRemoveAnonymousTaskByAdmin()
	{
		$anonymousUser = $this->entityManager
			->getRepository(User::class)
			->findOneBy(['id' => "2"]);

		$anonymousTasks = $this->entityManager
			->getRepository(Task::class)
			->findBy(['user' => $anonymousUser]);

		$targetTask = $anonymousTasks[random_int(0, count($anonymousTasks) - 1)];
		$idTargetTask = $targetTask->getId();

		$this->logUtils->login("admin");
		$urlDeleteTargetTask = "/tasks/" . $idTargetTask . "/delete";

		$crawler = $this->client->request('POST', $urlDeleteTargetTask);

		$this->assertEquals(302, $this->client->getResponse()->getStatusCode());

		$crawler = $this->client->followRedirect();
		$this->assertStringContainsString('La tâche a bien été supprimée', $crawler->text());

		$task = $this->entityManager
			->getRepository(Task::class)
			->findOneBy(['id' => $idTargetTask]);

		$this->assertEquals(null, $task);
	}

	/**
	 * Get the value of idCreatedTask
	 */
	public function getIdCreatedTask()
	{
		return $this->idCreatedTask;
	}

	/**
	 * Set the value of idCreatedTask
	 *
	 * @return  self
	 */
	public function setIdCreatedTask($idCreatedTask)
	{
		$this->idCreatedTask = $idCreatedTask;

		return $this;
	}
}
