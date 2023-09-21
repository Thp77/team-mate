<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


//Activer si on ne veux plus afficher les Teams sur la page d'accueil //

// class HomeController extends AbstractController
// {
//     #[Route('/', 'home.index', methods: ['GET'])]

//     public function index(): Response
//     {
//         return $this->render('pages/home.html.twig');
//     }
// }

//Controlleur pour afficher des articles sur la page d'accueil //

class HomeController extends AbstractController
{
    #[Route('/', 'home.index', methods: ['GET'])]

    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('pages/home.html.twig',
    ['teams' => $teamRepository->findPublicTeam(3)]);
    }
}
