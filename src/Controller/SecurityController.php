<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
	/**
	 * @Route("/login", name="login")
	 */
	public function login(AuthenticationUtils $authenticationUtils)
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
	 */
	public function logout()
	{
		throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
	}
}
