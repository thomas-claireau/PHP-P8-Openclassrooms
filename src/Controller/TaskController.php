<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Security\Voter\TaskVoter;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TaskController extends AbstractController
{
	/**
	 * @var Security
	 */
	private $security;

	/**
	 * @var User|null
	 */
	private $actualUser;

	/**
	 * @var AuthorizationCheckerInterface
	 */
	private $authorization;

	public function __construct(Security $security, AuthorizationCheckerInterface $authorizationCheckerInterface)
	{
		$this->security = $security;
		$this->actualUser = $this->security->getUser();
		$this->authorization = $authorizationCheckerInterface;
	}

	/**
	 * @Route("/tasks", name="task_list")
	 * 
	 * Method - listAction
	 * @param TaskRepository - $taskRepository
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(TaskRepository $taskRepository): Response
	{
		return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAllByUser($this->actualUser)]);
	}

	/**
	 * @Route("/tasks/done", name="task_list_done")
	 * 
	 * Method - listDoneTodo
	 * @param TaskRepository - $taskRepository
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function listDoneTodo(TaskRepository $taskRepository): Response
	{
		return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findDoneByUser($this->actualUser)]);
	}

	/**
	 * @Route("/tasks/todo", name="task_list_todo")
	 * 
	 * Method - listMakeTodo
	 * @param TaskRepository - $taskRepository
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function listMakeTodo(TaskRepository $taskRepository): Response
	{
		return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findTodoByUser($this->actualUser)]);
	}

	/**
	 * @Route("/tasks/create", name="task_create")
	 * 
	 * Method - createAction
	 * @param Request - $request
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function createAction(Request $request): Response
	{
		if (!$this->actualUser) {
			$this->addFlash('error', 'Vous devez être authentifié pour créer une tâche');

			return $this->redirectToRoute('task_list');
		}

		$date = new \DateTime();

		$task = new Task();
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$task->setCreatedAt($date);
			$task->setUpdatedAt($date);
			$task->setUser($this->actualUser);

			$em->persist($task);
			$em->flush();

			$this->addFlash('success', 'La tâche a été bien été ajoutée.');

			return $this->redirectToRoute('task_list');
		}

		return $this->render('task/create.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * @Route("/tasks/{id}/edit", name="task_edit")
	 * 
	 * Method - editAction
	 * @param Task - $task
	 * @param Request - $request
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Task $task, Request $request): Response
	{
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$task->setUpdatedAt(new \DateTime());
			$task->setUser($this->actualUser);
			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success', 'La tâche a bien été modifiée.');

			return $this->redirectToRoute('task_list');
		}

		return $this->render('task/edit.html.twig', [
			'form' => $form->createView(),
			'task' => $task,
		]);
	}

	/**
	 * @Route("/tasks/{id}/toggle", name="task_toggle")
	 * 
	 * Method - toggleTaskAction
	 * @param Task - $task
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function toggleTaskAction(Task $task): Response
	{
		$task->toggle(!$task->isDone());
		$task->setUpdatedAt(new \DateTime());
		$task->setUser($this->actualUser);
		$this->getDoctrine()->getManager()->flush();

		$this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

		return $this->redirectToRoute('task_list');
	}

	/**
	 * @Route("/tasks/{id}/delete", name="task_delete")
	 * 
	 * Method - deleteTaskAction
	 * @param Task - $task
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function deleteTaskAction(Task $task): Response
	{
		if (!$this->authorization->isGranted(TaskVoter::DELETE, $task)) {
			$this->addFlash('error', 'Vous ne pouvez pas supprimer cette tâche');
			return $this->redirectToRoute('task_list');
		}

		$em = $this->getDoctrine()->getManager();
		$em->remove($task);
		$em->flush();

		$this->addFlash('success', 'La tâche a bien été supprimée.');

		return $this->redirectToRoute('task_list');
	}
}
