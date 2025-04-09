<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    /**
     * Affiche la page d'accueil du site (visiteur)
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index() : Response {
        return $this->render('pages/home/index.html.twig');
    }
}
