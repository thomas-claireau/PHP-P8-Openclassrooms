<?php

namespace App\Tests\Unit\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
	private $routeTask = '/tasks';
	private $routeUser = '/users';

	/**
	 * Test access route when unauthenticated
	 * 
	 * @return void
	 */
	public function testAccessRouteWhenUnauthenticated()
	{
	}

	/**
	 * Test access route when authenticated
	 * 
	 * @return void
	 */
	public function testAccessRouteWhenAuthenticated()
	{
	}

	/**
	 * Test wrong username when login
	 * 
	 * @return void
	 */
	public function testWrongUsernameWhenLogin()
	{
	}

	/**
	 * Test incorrect format username when login
	 * 
	 * @return void
	 */
	public function testIncorrectFormatUsernameWhenLogin()
	{
	}

	/**
	 * Test wrong password when login
	 * 
	 * @return void
	 */
	public function testWrongPasswordWhenLogin()
	{
	}

	/**
	 * Test login with correct params
	 * 
	 * @return void
	 */
	public function testLoginWithCorrectParams()
	{
	}

	/**
	 * Test access user management with user role
	 * 
	 * @return void
	 */
	public function testAccessUserManagementWithUserRole()
	{
	}

	/**
	 * Test access user management with admin role
	 * 
	 * @return void
	 */
	public function testAccessUserManagementWithAdminRole()
	{
	}

	/**
	 * Test logout
	 * 
	 * @return void
	 */
	public function testLogout()
	{
	}
}
