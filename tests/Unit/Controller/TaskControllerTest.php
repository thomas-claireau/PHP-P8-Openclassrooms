<?php

namespace App\Tests\Unit\Controller;

use App\Repository\TaskRepository;
use App\Tests\LogUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
	private $client;

	public function setUp(): void
	{
		$this->client = static::createClient();
		$this->logUtils = new LogUtils($this->client);
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
		$this->logUtils->login("user");
		$crawler = $this->client->request('GET', '/tasks');

		$taskIsDoneBefore = filter_var($crawler->filter('.caption div.toggle')->first()->attr('data-is-done'), FILTER_VALIDATE_BOOLEAN);
		$formToggle = $crawler->selectButton("Marquer comme")->form();

		$this->client->submit($formToggle);

		$crawler = $this->client->followRedirect();
		$taskIsDoneAfter = filter_var($crawler->filter('.caption div.toggle')->first()->attr('data-is-done'), FILTER_VALIDATE_BOOLEAN);

		$this->assertNotEquals($taskIsDoneBefore, $taskIsDoneAfter);
	}

	/**
	 * Test remove task by unauthorized
	 * 
	 * @return void
	 */
	public function testRemoveTaskByUnauthorized()
	{
		$this->logUtils->login("user");
	}

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
