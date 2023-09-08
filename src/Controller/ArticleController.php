<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ArticleController extends AbstractController
{


    /**
     * Cette fonction affiche tout les Articles.
     *
     * @param ArticleRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/article', name: 'article.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]

    public function index(ArticleRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        // dd($article);

        $articles = $paginator->paginate(
            $repository->findBy(['user'=> $this->getUser()]),
            $request->query->getInt('page', 1),
            12
        );



        // Permet d'appeler les articles de la Bdd//

        return $this->render('pages/article/index.html.twig', [

            'articles' => $articles

        ]);
    }


    /**
     * Cette fonction permet de créer un Article
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/article/nouveau', name: 'article.new',  methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $article = $form->getData();
            $article->setUser(($this->getUser()));


            $manager->persist($article);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre article a bien été créé !'
            );
            return $this->redirectToRoute('article.index');
        }
        //////////////////////////////////
        return  $this->render('pages/article/new.html.twig', [

            'form' => $form->createView()

        ]);
    }


    
    #[Route('/article/edition/{id}', name: 'article.edit', methods: ['GET', 'POST'])]
     /**
     * Permet d'editer l'article
     *
     * @param ArticleRepository $repository
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(ArticleRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response

    {


        
        $article = $repository->findOneBy(["id" => $id]);
        $form = $this->createForm(ArticleType::class, $article);

        // condition autorisation d'édition d'article
        
        if ($this->getUser() !== $article->getUser()) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cet article.');
        }
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $article = $form->getData();

            $manager->persist($article);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre article a bien été modifié avec succès !'
            );
            return $this->redirectToRoute('article.index');
        }


        return  $this->render('pages/article/edit.html.twig', [

            'form' => $form->createView()

        ]);
    }


    #[Route('/article/suppression/{id}', name: 'article.delete', methods: ['GET'])]
    /**
     * Permet de supprimer l'article
     *
     * @param ArticleRepository $repository
     * @param EntityManagerInterface $manager
     * @param integer $id
     * @return Response
     */
    public function delete(ArticleRepository $repository, EntityManagerInterface $manager, int $id): Response
    {
        if (!$id) {

            $this->addFlash(
                'warning',
                'Votre article  n\'a pas été trouvé !'
            );
            return $this->redirectToRoute('article.index');
        }

        $article = $repository->find($id);
        $manager->remove($article);

        $manager->flush();


        $this->addFlash(
            'success',
            'Votre article a bien été supprimé avec succès !'
        );
        return $this->redirectToRoute('article.index');
    }
}
