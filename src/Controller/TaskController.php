<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    /**
     * Ajoute une nouvelle tâche à un projet
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/project/{id}/add-task', name: 'app_task_add')]
    public function addTask(Request $request, int $id): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project || !$project->isActive()) {
            return $this->redirectToRoute('app_projects_show');
        }

        $task = new Task();
        $task->setProject($project);

        $form = $this->createForm(TaskType::class, $task, ['project' => $project]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }

        return $this->render('pages/task/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Modification d'une tâche existante
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/task/edit/{id}', name: 'app_task_edit')]
    public function editTask(Request $request, int $id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->redirectToRoute('app_projects_show');
        }

        $project = $task->getProject();

        $form = $this->createForm(TaskType::class, $task, ['project' => $project]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }

        return $this->render('pages/task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView()
        ]);
    }

    /**
     * Supprime une tâche existante
     * @param int $id
     * @return Response
     */
    #[Route('/task/{id}/delete', name: 'app_task_delete')]
    public function deleteTask(int $id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if($task) {
            $project = $task->getProject()->getId();

            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project_show', ['id' => $project]);
        }

        return $this->redirectToRoute('app_projects_show');
    }
}
