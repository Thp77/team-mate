<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Service\TeamService;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    #[Route('/teams', name: 'team.index', methods: ['GET'])]
    

    public function index(Security $security,TeamRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Redirigez l'utilisateur vers la page de connexion ou effectuez une autre action appropriée
            // Par exemple, vous pouvez définir un message flash pour informer l'utilisateur
            $this->addFlash(
                'warning',
                'Vous devez être connecté pour acceder à cette page.'
            );
            return $this->redirectToRoute('home.index');
        }

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
     * Autorise l'acces uniquement au Team public
     *
     * @param Team $teams
     * @return Response
     */
    // #[Security("is_granted('ROLE_USER') and (teams.getIsPublic() === true)")]



    #[Route('/teams/show/{id}', name: 'team.show', methods: ['GET'])]

    public function show(TeamService $teamService, int $id): Response
    {
        $team = $teamService->get_team($id);
        return $this->render('pages/team/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/teams/indexpublic', name:'team.index.public', methods: ['GET'])]
    public function indexPublic(
        TeamRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {

        $teams = $paginator->paginate(
            $repository->findPublicTeam(null),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/team/index_public.html.twig', [
            'teams' => $teams
        ]);
    }



    /**
     * Permet de créer une team
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/teams/new', name: 'team.new', methods: ['GET', 'POST'])]

    public function new(EntityManagerInterface $manager, Request $request): Response

    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $team = $form->getData();
            $team->setUser($this->getUser());


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


    #[Route('/teams/{id}/edit', name: 'team.edit', methods: ['GET', 'POST'])]
    /**
     * Permet d'editer l'article
     *
     *
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(TeamService $teamService, int $id, Request $request, EntityManagerInterface $manager): Response

    {


        $team = $teamService->get_team($id);
        $form = $this->createForm(TeamType::class, $team);

        // condition autorisation d'édition de team


        if ($this->getUser() !== $team->getUser()) {
            $this->addFlash('warning', 'Vous n\'avez pas le droit d\'editer cet article.');
            return $this->redirectToRoute('team.index');;
            throw new AccessDeniedException;
        }


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

    #[Route('/teams/{id}/delete', name: 'team.delete', methods: ['GET'])]
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

        // condition autorisation delete l'article

        if ($this->getUser() !== $team->getUser()) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de supprimer cet article.');
            return $this->redirectToRoute('team.index');;
            throw new AccessDeniedException;
        }

        $this->addFlash(
            'success',
            'Votre team a bien été supprimé avec succès !'
        );
        return $this->redirectToRoute('team.index');
    }
}
