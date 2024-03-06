<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
   /*public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $articleRepository->findAll();
        $term = $request->query->get('q');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1), // page number
            10 // limit per page
        );

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'pagination' => $pagination,
        ]);
    }
*/
public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {

        
        $articles = $articleRepository->findAll();
        $term = $request->query->get('q');
        $pagination = $paginator->paginate(
            $articles,      
            $request->query->getInt('page', 1), // page number
            10 // limit per page
        );

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'pagination' => $pagination,
        ]);
    }
        #[Route('/back', name: 'app_article_index_back', methods: ['GET'])]
    public function back(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/show_back.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();

        if ($photoFile) {
            $newFilename = uniqid().'.'.$photoFile->guessExtension();
            $photoFile->move(
                $this->getParameter('photos_directory'),
                $newFilename
            );
            $article->setPhoto($newFilename);
        }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
     
    
    
    #[Route('/{id}/front', name: 'app_article_front_show', methods: ['GET'])]
    public function showfront(Article $article): Response
    {
        return $this->render('article/showfront.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index_back', [], Response::HTTP_SEE_OTHER);
    }
    
    








}
