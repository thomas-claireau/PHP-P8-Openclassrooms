<?php

namespace App\Tests\Functional;

use App\Tests\LogUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginPageTest extends WebTestCase
{
	private $client;

	public function setUp(): void
	{
		$this->client = static::createClient();
	}

	/**
	 * Test submit wrong authentication
	 * 
	 * @return void
	 */
	public function testSubmitWrongAuthentication()
	{
		$crawler = $this->client->request('GET', '/login');
		$loginForm = $crawler->selectButton("Se connecter")->form();

		$this->assertNotEquals(null, $loginForm);

		$loginForm['_username'] = 'xxx';
		$loginForm['_password'] = 'xxx';

		$crawler = $this->client->submit($loginForm);
		$crawler = $this->client->followRedirect();

		$this->assertStringContainsString('Invalid credentials.', $crawler->text());
	}

	/**
	 * Test submit correct authentication
	 * 
	 * @return void
	 */
	public function testSubmitCorrectAuthentication()
	{
		$crawler = $this->client->request('GET', '/login');
		$loginForm = $crawler->selectButton("Se connecter")->form();

		$this->assertNotEquals(null, $loginForm);

		$loginForm['_username'] = 'admin';
		$loginForm['_password'] = 'admin';

		$crawler = $this->client->submit($loginForm);
		$crawler = $this->client->followRedirect();

		$this->assertStringContainsString('Créer une nouvelle tâche', $crawler->text());
	}
}
