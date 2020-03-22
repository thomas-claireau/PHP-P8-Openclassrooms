<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
	/**
	 * @Route("/", name="homepage")
	 * 
	 * Method - indexAction -> render index.html.twig
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(): Response
	{
		return $this->render('default/index.html.twig');
	}
}
