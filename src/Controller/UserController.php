<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
	private $security;

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * @var User|null
	 */
	private $actualUser;

	public function __construct(Security $security, UserPasswordEncoderInterface $userPasswordEncoderInterface)
	{
		$this->security = $security;
		$this->encoder = $userPasswordEncoderInterface;
		$this->actualUser = $this->security->getUser();
	}
	/**
	 * @Route("/users", name="user_list")
	 */
	public function listAction(UserRepository $userRepository)
	{
		if ($this->actualUser->getRole() !== '["ROLE_ADMIN"]') {
			$this->addFlash('error', 'Vous ne pouvez pas accéder à cette partie du site');
			return $this->redirectToRoute('homepage');
		}

		return $this->render('user/list.html.twig', ['users' => $userRepository->findUsers()]);
	}

	/**
	 * @Route("/users/create", name="user_create")
	 */
	public function createAction(Request $request)
	{
		if ($this->actualUser->getRole() !== '["ROLE_ADMIN"]') {
			$this->addFlash('error', 'Vous ne pouvez pas accéder à cette partie du site');
			return $this->redirectToRoute('homepage');
		}

		$user = new User();
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$password = $this->encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($password);

			$em->persist($user);
			$em->flush();

			$this->addFlash('success', 'L\'utilisateur a bien été ajouté.');

			return $this->redirectToRoute('user_list');
		}

		return $this->render('user/create.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * @Route("/users/{id}/edit", name="user_edit")
	 */
	public function editAction(User $user, Request $request)
	{
		if ($this->actualUser->getRole() !== '["ROLE_ADMIN"]') {
			$this->addFlash('error', 'Vous ne pouvez pas accéder à cette partie du site');
			return $this->redirectToRoute('homepage');
		}

		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$password = $this->encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($password);

			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success', 'L\'utilisateur a bien été modifié');

			return $this->redirectToRoute('user_list');
		}

		return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
	}
}
