<?php

namespace App\Tests\Functional;

use App\Tests\LogUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\DefaultController
 * @covers \App\Controller\UserController
 * @covers \App\Entity\User
 */
class UserTest extends WebTestCase
{
	private $client;
	private $logUtils;

	public function setUp(): void
	{
		$this->client = static::createClient();
		$this->logUtils = new LogUtils($this->client);
	}

	/**
	 * Test redirect edit user button
	 * 
	 * @return void
	 */
	public function testRedirectEditUserButton()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/users');

		$users = $crawler->filter('table .user');
		$randomInt = random_int(0, $users->count() - 1);

		$selectedUser = $users->eq($randomInt);
		$username = $selectedUser->filter('.username')->text();
		$email = $selectedUser->filter('.email')->text();
		$linkAddUser = $selectedUser->selectLink("Edit")->link()->getUri();

		$crawler = $this->client->request('GET', $linkAddUser);

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Modifier " . $username, $titlePage);
	}

	/**
	 * Test form add user
	 * 
	 * @return void
	 */
	public function testFormAddUser()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/users/create');

		$titlePage = $crawler->filter('h1')->text();
		$this->assertStringContainsString("Créer un utilisateur", $titlePage);

		$createUserForm = $crawler->selectButton("Ajouter")->form();
		$usernameTest = 'Test create user 2';
		$passwordTest = "Test create user 2";
		$emailTest = 'test-create-user-2@test.fr';
		$roleTest = '["ROLE_USER"]';

		$createUserForm['user[username]'] = $usernameTest;
		$createUserForm['user[password][first]'] = $passwordTest;
		$createUserForm['user[password][second]'] = $passwordTest;
		$createUserForm['user[email]'] = $emailTest;
		$createUserForm['user[role]'] = $roleTest;

		$crawler = $this->client->submit($createUserForm);

		$crawler = $this->client->followRedirect();

		$successMessage = $crawler->filter('div.alert.alert-success')->text();
		$user = $crawler->filter('.user')->first();
		$username = $user->filter('.username')->text();
		$email = $user->filter('.email')->text();

		$this->assertStringContainsString("L'utilisateur a bien été ajouté.", $successMessage);
		$this->assertStringContainsString($usernameTest, $username);
		$this->assertStringContainsString($emailTest, $email);
	}

	/**
	 * Test form edit user
	 * 
	 * @return void
	 */
	public function testFormEditUser()
	{
		$this->logUtils->login('admin');
		$crawler = $this->client->request('GET', '/users');

		$updatedUser = $crawler->filter(".user")->first();
		$username = $updatedUser->filter('.username')->text();
		$email = $updatedUser->filter('.email')->text();
		$linkUpdatedUser = $updatedUser->selectLink("Edit")->link()->getUri();

		$crawler = $this->client->request('GET', $linkUpdatedUser);

		$updateUserForm = $crawler->selectButton("Modifier")->form();
		$usernameUser = $crawler->filter('input[name="user[username]"]')->extract(array('value'))[0];
		$emailUser = $crawler->filter('input[name="user[email]"]')->extract(array('value'))[0];

		$this->assertNotEquals(null, $updateUserForm);
		$this->assertEquals($username, $usernameUser);
		$this->assertEquals($email, $emailUser);

		$updatedUsername = 'Update ' . $usernameUser;
		$updatedEmail = $emailUser;
		$updatedEmail = explode('@', $emailUser);
		$updatedEmail = $updatedEmail[0] . '-update@' . $updatedEmail[1];

		$updateUserForm['user[username]'] = $updatedUsername;
		$updateUserForm['user[email]'] = $updatedEmail;

		$crawler = $this->client->submit($updateUserForm);

		$crawler = $this->client->followRedirect();

		$successMessage = $crawler->filter('div.alert.alert-success')->text();
		$user = $crawler->filter('.user')->first();
		$username = $user->filter('.username')->text();
		$email = $user->filter('.email')->text();

		$this->assertStringContainsString("L'utilisateur a bien été modifié", $successMessage);
		$this->assertStringContainsString($updatedUsername, $username);
		$this->assertStringContainsString($updatedEmail, $email);
	}
}
