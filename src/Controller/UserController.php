<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route('/user', name: 'app_users_show')]
    public function showUsers() : Response
    {
        return $this->render('pages/user/show.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_edit')]
    public function editUser(Request $request, int $id) : Response
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->redirectToRoute('app_users_show');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_users_show');
        }

        return $this->render('pages/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_user_delete')]
    public function deleteUser(int $id) : Response
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->redirectToRoute('app_users_show');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_users_show');
    }
}
