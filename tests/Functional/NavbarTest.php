<?php

namespace App\Tests\Functional;

use App\Tests\LogUtils;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\DefaultController
 * @covers \App\Controller\UserController
 * @covers \App\Controller\TaskController
 * @covers \App\Controller\SecurityController
 * @covers \App\Entity\User
 * @covers \App\Entity\Task
 */
class NavbarTest extends WebTestCase
{
	private $client;
	private $logUtils;

	public function setUp(): void
	{
		$this->client = static::createClient();
		$this->logUtils = new LogUtils($this->client);
	}

	/**
	 * Test redirect home title
	 * 
	 * @return void
	 */
	public function testRedirectHomeTitle()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$linkRedirectHomeTitle = $crawler->selectLink("To Do List app")->link()->getUri();

		$crawler = $this->client->request('GET', $linkRedirectHomeTitle);


		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$titlePage = $crawler->filter('div#page-body')->text();
		$this->assertStringContainsString("Créer une nouvelle tâche", $titlePage);
	}

	/**
	 * Test redirect home link
	 * 
	 * @return void
	 */
	public function testRedirectHomeLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$linkRedirectHomeLink = $crawler->selectLink("Accueil")->link()->getUri();

		$crawler = $this->client->request('GET', $linkRedirectHomeLink);


		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$titlePage = $crawler->filter('div#page-body')->text();
		$this->assertStringContainsString("Créer une nouvelle tâche", $titlePage);
	}

	/**
	 * Test all tasks link
	 * 
	 * @return void
	 */
	public function testAllTasksLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$linkAllTasks = $crawler->selectLink("Toutes les tâches")->link()->getUri();
		$crawler = $this->client->request('GET', $linkAllTasks);
		$taskDone = $crawler->filter('.caption div.toggle[data-is-done="true"]');
		$taskToDo = $crawler->filter('.caption div.toggle[data-is-done="false"]');

		$this->assertNotEquals(null, $taskDone);
		$this->assertNotEquals(null, $taskToDo);
	}

	/**
	 * Test tasks to do link
	 * 
	 * @return void
	 */
	public function testTasksToDoLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$linkTasksToDo = $crawler->selectLink("Tâches à effectuer")->link()->getUri();
		$crawler = $this->client->request('GET', $linkTasksToDo);

		$crawler->filter('.caption div.toggle')->each(function (Crawler $node, $i) {
			$taskToggle = filter_var($node->attr('data-is-done'), FILTER_VALIDATE_BOOLEAN);

			$this->assertEquals(false, $taskToggle);
		});
	}

	/**
	 * Test tasks done link
	 * 
	 * @return void
	 */
	public function testTasksDoneLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$linkTasksDone = $crawler->selectLink("Tâches effectuées")->link()->getUri();
		$crawler = $this->client->request('GET', $linkTasksDone);

		$crawler->filter('.caption div.toggle')->each(function (Crawler $node, $i) {
			$taskToggle = filter_var($node->attr('data-is-done'), FILTER_VALIDATE_BOOLEAN);

			$this->assertEquals(true, $taskToggle);
		});
	}

	/**
	 * Test create task link
	 * 
	 * @return void
	 */
	public function testCreateTaskLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$linkCreateTask = $crawler->selectLink("Créer une nouvelle tâche")->link()->getUri();
		$crawler = $this->client->request('GET', $linkCreateTask);

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Créer une tâche", $titlePage);
	}

	/**
	 * Test display user links only admin role
	 * 
	 * @return void
	 */
	public function testDisplayUserLinksOnlyAdminRole()
	{
		// test without admin role
		$this->logUtils->login('user');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$navbar = $crawler->filter('#navbar')->text();
		$this->assertStringNotContainsString("Utilisateurs", $navbar);

		// test with admin role
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$navbar = $crawler->filter('#navbar')->text();
		$this->assertStringContainsString("Utilisateurs", $navbar);
	}

	/**
	 * Test all users link
	 * 
	 * @return void
	 */
	public function testAllUsersLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$navbar = $crawler->filter('#navbar')->text();
		$this->assertStringContainsString("Utilisateurs", $navbar);

		$linkAllUsers = $crawler->selectLink("Liste d'utilisateur")->link()->getUri();
		$crawler = $this->client->request('GET', $linkAllUsers);

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Liste des utilisateurs", $titlePage);
	}

	/**
	 * Test create user link
	 * 
	 * @return void
	 */
	public function testCreateUserLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$navbar = $crawler->filter('#navbar')->text();
		$this->assertStringContainsString("Utilisateurs", $navbar);

		$linkCreateUser = $crawler->selectLink("Créer un utilisateur")->link()->getUri();
		$crawler = $this->client->request('GET', $linkCreateUser);

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Créer un utilisateur", $titlePage);
	}

	/**
	 * Test logout link
	 * 
	 * @return void
	 */
	public function testLogoutLink()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/');

		$this->assertStringContainsString("Créer une nouvelle tâche", $crawler->text());

		$navbar = $crawler->filter('#navbar')->text();
		$this->assertStringContainsString("Se déconnecter", $navbar);

		$linkLogout = $crawler->selectLink("Se déconnecter")->link()->getUri();
		$crawler = $this->client->request('GET', $linkLogout);

		$crawler = $this->client->followRedirect();
		$crawler = $this->client->followRedirect();

		$btnLogin = $crawler->filter('form .btn-success')->text();
		$this->assertStringContainsString("Se connecter", $btnLogin);
	}
}
