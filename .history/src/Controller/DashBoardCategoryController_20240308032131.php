<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Category1Type;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dash/board/category')]
class DashBoardCategoryController extends AbstractController
{
    #[Route('/', name: 'app_dash_board_category_index', methods: ['GET', 'POST'])]
    public function index(CategoryRepository $categoryRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(Category1Type::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle photo upload
            $photoFile = $form->get('photoURL')->getData();
            if ($photoFile) {
                // Generate a unique filename
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where photos are stored
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                // Set the photo URL in the Category entity
                $category->setPhotoURL($newFilename);
            }

            // Persist the entity
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_dash_board_category_index', [], Response::HTTP_SEE_OTHER);
        }
        $sortBy = $request->query->get('sortBy', 'id');
        $order = $request->query->get('order', 'ASC');
        $sortOrder = $order === 'ASC' ? 'DESC' : 'ASC';

        // Retrieve sorted categories using the repository method
        $categories = $categoryRepository->findAllWithSorting($sortBy, $order);

        return $this->render('dash_board_category/index.html.twig', [           
            'categories' => $categoryRepository->findAll(),
            'categories' => $categories,
            'form' => $form->createView(),
            'sortOrder' => $sortOrder,

    ]);
    }
    #[Route('/new', name: 'app_dash_board_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(Category1Type::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_dash_board_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dash_board_category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_dash_board_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('dash_board_category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dash_board_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('The category does not exist');
        }
        
        $form = $this->createForm(Category1Type::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // Handle photo upload
             $photoFile = $form->get('photoURL')->getData();
             if ($photoFile) {
                 // Generate a unique filename
                 $newFilename = uniqid().'.'.$photoFile->guessExtension();
 
                 // Move the file to the directory where photos are stored
                 $photoFile->move(
                     $this->getParameter('photos_directory'),
                     $newFilename
                 );
 
                 // Set the photo URL in the Category entity
                 $category->setPhotoURL($newFilename);
             }
 
             // Persist the entity
             $entityManager->persist($category);
             $entityManager->flush();

            return $this->redirectToRoute('app_dash_board_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dash_board_category/editcat.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_dash_board_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager , $id): Response
    {
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('The category does not exist');
        }
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dash_board_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
