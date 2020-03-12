<?php

namespace App\Tests\Unit\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class SecurityControllerTest extends WebTestCase
{
	private $client;

	const ROUTE_TASK = "/tasks";
	const ROUTE_USER = "/users";
	const ADMIN = ['username' => 'root', 'password' => 'root'];
	const USER = ['username' => 'user', 'password' => 'user'];

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
		$this->client->request('GET', self::ROUTE_TASK);
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
		$this->client->request('GET', self::ROUTE_TASK);
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
		$this->client->request('GET', self::ROUTE_TASK);
		$this->assertEquals('200', $this->client->getResponse()->getStatusCode());
	}

	/**
	 * Test wrong username when login
	 * 
	 * @return void
	 */
	// public function testWrongUsernameWhenLogin()
	// {
	// }

	/**
	 * Test incorrect format username when login
	 * 
	 * @return void
	 */
	// public function testIncorrectFormatUsernameWhenLogin()
	// {
	// }

	/**
	 * Test wrong password when login
	 * 
	 * @return void
	 */
	// public function testWrongPasswordWhenLogin()
	// {
	// }

	/**
	 * Test login with correct params
	 * 
	 * @return void
	 */
	// public function testLoginWithCorrectParams()
	// {
	// }

	/**
	 * Test access user management with user role
	 * 
	 * @return void
	 */
	// public function testAccessUserManagementWithUserRole()
	// {
	// }

	/**
	 * Test access user management with admin role
	 * 
	 * @return void
	 */
	// public function testAccessUserManagementWithAdminRole()
	// {
	// }

	/**
	 * Test logout
	 * 
	 * @return void
	 */
	// public function testLogout()
	// {
	// }

	public function logIn($isAdmin)
	{
		$credentials = $isAdmin == 'admin' ? self::ADMIN : self::USER;

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
