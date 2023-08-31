<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function index(ArticleRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        // dd($article);

        $articles = $paginator->paginate(
            $repository->findAll(),
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
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        /**
         * Envoie du formulaire 
         */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $article = $form->getData();

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
    public function edit(ArticleRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response

    {

        $article = $repository->findOneBy(["id" => $id]);
        $form = $this->createForm(ArticleType::class, $article);

        /**
         * Envoie du formulaire modifié
         */
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
    public function delete(ArticleRepository $repository, EntityManagerInterface $manager, int $id): Response
    {
        if (!$id) {

            $this->addFlash(
                'warning',
                'Votre article est n\'a pas été trouvé !'
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
