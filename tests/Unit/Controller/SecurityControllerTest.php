<?php

namespace App\Tests\Unit\Controller;

use App\Tests\LogUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class SecurityControllerTest extends WebTestCase
{
	private $client;
	private $logUtils;

	public function setUp(): void
	{
		$this->client = static::createClient();
		$this->logUtils = new LogUtils($this->client);
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
		$this->logUtils->login('admin');
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
		$this->logUtils->login('user');
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
		$this->logUtils->login('user');
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
		$this->logUtils->login('admin');
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
		$this->logUtils->login('admin');
		$this->client->request('GET', "/logout");
		$this->client->request('GET', "/tasks");
		$this->assertEquals('302', $this->client->getResponse()->getStatusCode());
	}
}
