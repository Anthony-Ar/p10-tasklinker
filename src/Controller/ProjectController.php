<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Status;
use App\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class ProjectController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    /**
     * Affiche la liste des projets
     * @return Response
     */
    #[Route('/', name: 'app_projects_show')]
    public function showProjects(): Response
    {
        return $this->render('pages/project/show.html.twig', [
            'projects' => $this->entityManager->getRepository(Project::class)->findBy(['active' => true]),
        ]);
    }

    /**
     * Affiche le détail d'un projet
     * @param int $id
     * @return Response
     */
    #[Route('/project/{id}', name: 'app_project_show', requirements: ['id' => '\d+'])]
    public function showProject(int $id): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project || !$project->isActive()) {
            return $this->redirectToRoute('app_projects_show');
        }

        return $this->render('pages/project/show_detail.html.twig', [
            'status' => $this->entityManager->getRepository(Status::class)->findAll(),
            'project' => $project
        ]);
    }

    /**
     * Ajoute un nouveau projet à la base de données
     * @param Request $request
     * @return Response
     */
    #[Route('/project/add', name: 'app_project_add')]
    public function addProject(Request $request): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }

        return $this->render('pages/project/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Modifie un projet existant
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/project/{id}/edit', name: 'app_project_edit')]
    public function editProject(Request $request, int $id): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if(!$project || !$project->isActive()) {
            return $this->redirectToRoute('app_projects_show');
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }

        return $this->render('pages/project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * Soft delete un projet existant
     * @param int $id
     * @return Response
     */
    #[Route('/project/{id}/delete', name: 'app_project_delete')]
    public function deleteProject(int $id): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if($project) {
//            $this->entityManager->remove($project);
//            $this->entityManager->flush();

            // Soft delete
            $project->setActive(false);
            $this->entityManager->persist($project);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_projects_show');
    }
}
