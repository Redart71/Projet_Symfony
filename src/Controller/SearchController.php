<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/article/search', name: 'article_search_text')]
    public function searchArticle(ManagerRegistry $doctrine, Request $request)
    {
        $text = $request->request->get("form");
        dump($text);
        $repository = $doctrine->getRepository(Article::class);
        $filteredArticles = []; // $repository->findBy(["title" => $text]);
        return $this->render('article/search_article.html.twig', [
            "filteredArticles" => $filteredArticles
        ]);    
    }
}