<?php

namespace App\Tests\Unit\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class SecurityControllerTest extends WebTestCase
{
	private $client;

	public function setUp(): void
	{
		$this->client = static::createClient();
	}

	/**
	 * Test access route when unauthenticated
	 * 
	 * @return void
	 */
	public function testAccessRouteWhenUnauthenticated()
	{
		$this->client->request('GET', "/tasks");
		$this->assertEquals('302', $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test access route when authenticated with admin role
	 * 
	 * @return void
	 */
	public function testAccessRouteWhenAuthenticatedWithAdminRole()
	{
		$this->logIn('admin');
		$this->client->request('GET', "/tasks");
		$this->assertEquals('200', $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test access route when authenticated with user role
	 * 
	 * @return void
	 */
	public function testAccessRouteWhenAuthenticatedWithUserRole()
	{
		$this->logIn('user');
		$this->client->request('GET', "/tasks");
		$this->assertEquals('200', $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test wrong credentials when login
	 * 
	 * @return void
	 */
	public function testWrongCredientialsWhenLogin()
	{
		$this->client->request('POST', "/login_check", ['_username' => 'xxx', '_password' => 'xxx']);
		$crawler = $this->client->followRedirect();
		$this->assertStringContainsString('Invalid credentials.', $crawler->text());
	}

	/**
	 * Test login with correct params (eq. admin)
	 * 
	 * @return void
	 */
	public function testLoginWithCorrectParams()
	{
		$this->client->request('POST', "/login_check", ['_username' => 'root', '_password' => 'root']);
		$crawler = $this->client->followRedirect();
		$this->assertStringContainsString('Créer une nouvelle tâche', $crawler->text());
	}

	/**
	 * Test access user management with user role
	 * 
	 * @return void
	 */
	public function testAccessUserManagementWithUserRole()
	{
		$this->logIn('user');
		$this->client->request('GET', "/users");
		$this->assertEquals('302', $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test access user management with admin role
	 * 
	 * @return void
	 */
	public function testAccessUserManagementWithAdminRole()
	{
		$this->logIn('admin');
		$this->client->request('GET', "/users");
		$this->assertEquals('200', $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test logout
	 * 
	 * @return void
	 */
	public function testLogout()
	{
		$this->logIn('admin');
		$this->client->request('GET', "/logout");
		$this->client->request('GET', "/tasks");
		$this->assertEquals('302', $this->client->getResponse()->getStatusCode());
	}

	public function logIn($isAdmin)
	{
		$adminCredentials = ['username' => 'root', 'password' => 'root'];
		$userCredentials = ['username' => 'user', 'password' => 'user'];

		$credentials = $isAdmin == 'admin' ? $adminCredentials : $userCredentials;

		// get doctrine
		$entityManager = $this->client->getContainer()
			->get('doctrine')
			->getManager();

		// get a user from database
		$user = $entityManager
			->getRepository(User::class)
			->findOneBy([
				'username' => $credentials['username']
			]);


		$session = $this->client->getContainer()->get('session');

		$firewall = 'main';
		$token = new UsernamePasswordToken($user, $credentials['password'], $firewall, $user->getRoles());

		$session->set('_security_' . $firewall, serialize($token));
		$session->save();

		$cookie = new Cookie($session->getName(), $session->getId());
		$this->client->getCookieJar()->set($cookie);
	}
}
