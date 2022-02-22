<?php


namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentControler extends AbstractController
{
    #[Route('/comment/create', name: 'commentaire')]
    public function newComment(Request $request, ManagerRegistry $doctrine): Response
    {
        $commentaire = new Comment();
        $form = $this->createForm(CommentType::class, $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($commentaire);
            $entityManager->flush();
        }
        return $this->render('comment/newcommentaire.html.twig', [
            'form' => $form->createView()

        ]);
    }
}
