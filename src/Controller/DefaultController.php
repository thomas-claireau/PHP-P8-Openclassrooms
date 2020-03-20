<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
	/**
	 * @Route("/", name="homepage")
	 */
	public function indexAction()
	{
		return $this->render('default/index.html.twig');
	}

	/**
	 * @Route("/phpinfo", name="phpinfo")
	 */
	public function phpinfo()
	{
		ob_start();
		phpinfo();
		$str = ob_get_contents();
		ob_get_clean();

		return new Response($str);
	}
}
