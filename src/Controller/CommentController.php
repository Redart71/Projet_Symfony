<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/comment/create/{id}', name: 'commentaire')]
    public function newComment(Request $request, ManagerRegistry $doctrine, Article $article): Response
    {
        $commentaire = new Comment();
        $form = $this->createForm(CommentType::class, $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $commentaire->setArticle($article);
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('read_article',[
                "id" => $article->getId(),
            ]);
        }
        return $this->render('comment/newcommentaire.html.twig', [
            'form' => $form->createView(),
            "articleId" => $article->getId()

        ]);
    }
}
