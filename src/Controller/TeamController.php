<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    /**
     *  Cette fonction affiche toutes les teams.
     *
     * @param TeamRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/team', name: 'team.index', methods: ['GET'])]
    public function index(TeamRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {


        $teams = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('pages/team/index.html.twig', [
            'teams' => $teams,
        ]);
    }

    #[Route('/team/creation', name: 'team.new', methods: ['GET','POST'])]
    public function new(): Response

    {
        $team = new Team();
        $form = $this-> createForm(TeamType::class, $team);
        return $this->render('pages/team/new.html.twig', 
        [
            'form' => $form->createView()
        ]);

    }
}
