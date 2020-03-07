<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
	private $security;

	public function __construct(Security $security)
	{
		$this->security = $security;
	}
	/**
	 * @Route("/tasks", name="task_list")
	 */
	public function listAction(TaskRepository $taskRepository)
	{
		$user = $this->security->getUser();
		return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAllByUser($user), 'user' => $user]);
	}

	/**
	 * @Route("/tasks/create", name="task_create")
	 */
	public function createAction(Request $request)
	{
		$user = $this->security->getUser();
		if (!$user) return;

		$date = new \DateTime();

		$task = new Task();
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$task->setCreatedAt($date);
			$task->setUpdatedAt($date);
			$task->setUser($user);

			$em->persist($task);
			$em->flush();

			$this->addFlash('success', 'La tâche a été bien été ajoutée.');

			return $this->redirectToRoute('task_list');
		}

		return $this->render('task/create.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * @Route("/tasks/{id}/edit", name="task_edit")
	 */
	public function editAction(Task $task, Request $request)
	{
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$task->setUpdatedAt(new \DateTime());
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
	 */
	public function toggleTaskAction(Task $task)
	{
		$task->toggle(!$task->isDone());
		$task->setUpdatedAt(new \DateTime());
		$this->getDoctrine()->getManager()->flush();

		$this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

		return $this->redirectToRoute('task_list');
	}

	/**
	 * @Route("/tasks/{id}/delete", name="task_delete")
	 */
	public function deleteTaskAction(Task $task)
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($task);
		$em->flush();

		$this->addFlash('success', 'La tâche a bien été supprimée.');

		return $this->redirectToRoute('task_list');
	}
}
