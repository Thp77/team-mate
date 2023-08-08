<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        // dd($article);

        $articles = $paginator->paginate(
            $repository->findAll(), 
            $request->query->getInt('page', 1), 10
        );
    


        // Permet d'appeler les articles de la Bdd
        
        return $this->render('pages/article/index.html.twig', [

            'articles' => $articles

        ]);
    }
}
