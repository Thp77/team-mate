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
use Doctrine\ORM\EntityManagerInterface;

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




    /**
     * Permet de créer une team
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/team/creation', name: 'team.new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $manager, Request $request): Response

    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $team = $form->getData();

            $manager->persist($team);
            $manager->flush();


            $this->addFlash(
                'success',
                'Votre Team a bien été créé !'
            );

            return $this->redirectToRoute('team.index');
        }

        return $this->render(
            'pages/team/new.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }


    #[Route('/team/edition/{id}', name: 'team.edit', methods: ['GET', 'POST'])]
    /**
     * Permet d'editer l'article
     *
     * @param TeamRepository $repository
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(TeamRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response

    {
        

        $team = $repository->findOneBy(["id" => $id]);
        $form = $this->createForm(TeamType::class, $team);

        /**
         * Envoie du formulaire modifié
         */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $team = $form->getData();

            $manager->persist($team);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre team a bien été modifié avec succès !'
            );
            return $this->redirectToRoute('team.index');
        }


        return  $this->render('pages/team/edit.html.twig', [

            'form' => $form->createView()

        ]);
    }

    #[Route('/team/suppression/{id}', name: 'team.delete', methods: ['GET'])]
    /**
     * Permet de supprimer l'article
     *
     * @param TeamRepository $repository
     * @param EntityManagerInterface $manager
     * @param integer $id
     * @return Response
     */
    public function delete(TeamRepository $repository, EntityManagerInterface $manager, int $id): Response
    {
        if (!$id) {

            $this->addFlash(
                'warning',
                'Votre Team  n\'a pas été trouvé !'
            );
            return $this->redirectToRoute('team.index');
        }

        $team = $repository->find($id);
        $manager->remove($team);

        $manager->flush();


        $this->addFlash(
            'success',
            'Votre team a bien été supprimé avec succès !'
        );
        return $this->redirectToRoute('team.index');
    }
}
