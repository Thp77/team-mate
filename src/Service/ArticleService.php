<?php


namespace App\Service;

use App\Entity\Article;
use App\Repository\ArticleRepository;

class ArticleService {

    public function __construct(private ArticleRepository $articleRepository)
    {
        
    }

    public function get_Article(int $id) : Article {
        $article = $this->articleRepository->find($id);
        return $article;
    }
}

