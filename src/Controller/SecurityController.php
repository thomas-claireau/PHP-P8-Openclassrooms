<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
	/**
	 * @Route("/login", name="login")
	 * 
	 * Method - Login user
	 * @param AuthenticationUtils - $authenticationUtils
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function login(AuthenticationUtils $authenticationUtils): Response
	{
		if ($this->getUser() == null) {
			$error = $authenticationUtils->getLastAuthenticationError();
			$lastUsername = $authenticationUtils->getLastUsername();

			return $this->render('security/login.html.twig', array(
				'last_username' => $lastUsername,
				'error'         => $error,
			));
		}

		return $this->redirectToRoute('homepage');
	}

	/**
	 * @Route("/logout", name="logout")
	 * 
	 * Method - Logout user
	 * @return \Exception
	 */
	public function logout(): Exception
	{
		throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
	}
}
