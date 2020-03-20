<?php

namespace App\Tests\Functional;

use App\Entity\Task;
use App\Tests\LogUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
	private $client;
	private $logUtils;
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
	 * Test redirect create task button
	 * 
	 * @return void
	 */
	public function testRedirectCreateTaskButton()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/tasks');
		$linkAddTask = $crawler->selectLink("Créer une tâche")->link()->getUri();

		$crawler = $this->client->request('GET', $linkAddTask);

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Créer une tâche", $titlePage);
	}

	/**
	 * Test form add task
	 * 
	 * @return void
	 */
	public function testFormAddTask()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/tasks/create');

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Créer une tâche", $titlePage);

		$createTaskForm = $crawler->selectButton("Ajouter")->form();
		$titleTest = 'Test title task with functionnal testFormAddTask';
		$contentTest = 'Test content task with functionnal testFormAddTask';

		$createTaskForm['task[title]'] = $titleTest;
		$createTaskForm['task[content]'] = $contentTest;

		$crawler = $this->client->submit($createTaskForm);

		$crawler = $this->client->followRedirect();

		$successMessage = $crawler->filter('div.alert.alert-success')->text();
		$titleTask = $crawler->filter('.caption .portlet-header')->first()->text();
		$contentTask = $crawler->filter('.caption .inner .content')->first()->text();

		$this->assertStringContainsString('La tâche a été bien été ajoutée.', $successMessage);
		$this->assertStringContainsString($titleTest, $titleTask);
		$this->assertStringContainsString($contentTest, $contentTask);
	}

	/**
	 * Test redirect after task added
	 * 
	 * @return void
	 */
	public function testRedirectAfterTaskAdded()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/tasks/create');

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Créer une tâche", $titlePage);

		$createTaskForm = $crawler->selectButton("Ajouter")->form();
		$titleTest = 'Test title task with functionnal testFormAddTask';
		$contentTest = 'Test content task with functionnal testFormAddTask';

		$createTaskForm['task[title]'] = $titleTest;
		$createTaskForm['task[content]'] = $contentTest;

		$crawler = $this->client->submit($createTaskForm);

		$this->assertEquals(302, $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test back to tasks button
	 * 
	 * @return void
	 */
	public function testBackToTasksButton()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/tasks/create');

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Créer une tâche", $titlePage);

		$linkBackToTasks = $crawler->selectLink("Retour à la liste des tâches")->link()->getUri();

		$crawler = $this->client->request('GET', $linkBackToTasks);

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$taskDone = $crawler->filter('.caption div.toggle[data-is-done="true"]');
		$taskToDo = $crawler->filter('.caption div.toggle[data-is-done="false"]');

		$this->assertNotEquals(null, $taskDone);
		$this->assertNotEquals(null, $taskToDo);
	}

	/**
	 * Test toggle task button
	 * 
	 * @return void
	 */
	public function testToggleTaskButton()
	{
		$this->logUtils->login("admin");
		$crawler = $this->client->request('GET', '/tasks');

		$taskIsDoneBefore = filter_var($crawler->filter('.caption div.toggle')->first()->attr('data-is-done'), FILTER_VALIDATE_BOOLEAN);
		$formToggle = $crawler->selectButton("Marquer comme")->form();

		$this->client->submit($formToggle);

		$crawler = $this->client->followRedirect();
		$taskIsDoneAfter = filter_var($crawler->filter('.caption div.toggle')->first()->attr('data-is-done'), FILTER_VALIDATE_BOOLEAN);

		$this->assertNotEquals($taskIsDoneBefore, $taskIsDoneAfter);
	}

	/**
	 * Test redirect edit task link
	 * 
	 * @return void
	 */
	public function testRedirectEditTaskLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/tasks');

		$updatedTask = $crawler->filter(".task")->first();

		$title = $updatedTask->filter('.link')->text();
		$content = $updatedTask->filter('.portlet-content.content')->text();
		$link = $updatedTask->filter('.link')->link()->getUri();

		$crawler = $this->client->request('GET', $link);

		$titlePage = $crawler->filter('h1')->text();
		$titleTask = $crawler->filter('input[name="task[title]"]')->extract(array('value'))[0];
		$contentTask = $crawler->filter('textarea[name="task[content]"]')->text();

		$this->assertStringContainsString("Editer la tâche", $titlePage);
		$this->assertEquals($title, $titleTask);
		$this->assertEquals($content, $contentTask);
	}

	/**
	 * Test form edit task
	 * 
	 * @return void
	 */
	public function testFormEditTask()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/tasks');

		$updatedTask = $crawler->filter(".task")->first();

		$title = $updatedTask->filter('.link')->text();
		$content = $updatedTask->filter('.portlet-content.content')->text();
		$link = $updatedTask->filter('.link')->link()->getUri();

		$crawler = $this->client->request('GET', $link);

		$updateTaskForm = $crawler->selectButton("Modifier")->form();

		$updateTaskForm['task[title]'] = 'Update ' . $title;
		$updateTaskForm['task[content]'] = 'Update ' . $content;

		$crawler = $this->client->submit($updateTaskForm);

		$crawler = $this->client->followRedirect();

		$successMessage = $crawler->filter('div.alert.alert-success')->text();
		$titleTask = $crawler->filter('.caption .portlet-header')->first()->text();
		$contentTask = $crawler->filter('.caption .inner .content')->first()->text();

		$this->assertStringContainsString('La tâche a bien été modifiée.', $successMessage);
		$this->assertStringContainsString('Update ' . $title, $titleTask);
		$this->assertStringContainsString('Update ' . $content, $contentTask);
	}

	/**
	 * Test remove task button
	 * 
	 * @return void
	 */
	public function testRemoveTaskButton()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/tasks');

		$taskToRemoved = $crawler->filter(".task")->first();
		$id = $taskToRemoved->attr('data-id');
		$removeTaskForm = $taskToRemoved->selectButton("Supprimer")->form();

		$crawler = $this->client->submit($removeTaskForm);

		$crawler = $this->client->followRedirect();

		$successMessage = $crawler->filter('div.alert.alert-success')->text();

		$task = $this->entityManager
			->getRepository(Task::class)
			->findOneBy(['id' => $id]);

		$this->assertStringContainsString('La tâche a bien été supprimée.', $successMessage);
		$this->assertEquals(null, $task);
	}
}
