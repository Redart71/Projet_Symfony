<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleController extends AbstractController
{
    #[Route('/article/create', name: 'article')]
    public function createArticle(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            $entityManager = $doctrine->getManager();
            $article->setCreatedAt(new DateTime());

            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $article->setPicture($newFilename);
                $entityManager->persist($article);
                $entityManager->flush();
            }
            return $this->redirectToRoute('read_all_article');
        }
        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/', name: 'read_all_article')]
    public function readAll(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Article::class);
        $articles = $repository->findAll();

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('article_search_text'))
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->getForm();

        return $this->render('article/read_all.html.twig', [
            "articles" => $articles,
            "form" => $form->createView()
        ]);
    }
    #[Route('/article/read/{id}', name: 'read_article')]
    public function read(ManagerRegistry $doctrine, Article $article): Response
    {
        return $this->render('article/read.html.twig', [
            "article" => $article
        ]);
    }
    #[Route('/article/edit/{id}', name: 'edit_article')]
    public function edit(Request $request, ManagerRegistry $doctrine, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('read_all_article');
        }
        return $this->render('article/edit.html.twig', [
            "form" => $form->createView()
        ]);
    }
    #[Route('/article/delete/{id}', name: 'delete_article')]
    public function delete(ManagerRegistry $doctrine, Article $article): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute("read_all_article");
    }
}
