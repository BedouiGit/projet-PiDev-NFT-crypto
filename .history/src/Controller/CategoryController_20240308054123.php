<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode;


#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
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
    
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request , EntityManagerInterface $entityManager , $id): Response
    {

        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('The category does not exist');
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoURL')->getData();
            if ($photoFile) {
                // Générez un nom de fichier unique
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                // Déplacez le fichier vers le répertoire où sont stockées les photos
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                // Mettez à jour l'URL de l'image dans l'entité Project
                $category->setPhotoURL($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager , $id): Response
    {

        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('The category does not exist');
        }
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {

            $projets = $category->getProjets();

            foreach($projets as $projet){
                $entityManager->remove($projet);
            }
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/qrcode', name: 'app_category_qrcode', methods: ['GET'])]
    public function generateQrCode(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
    
        // Create an empty QR code
        $qrCode = new QrCod();
    
        // Loop through categories and add their data to the QR code
        foreach ($categories as $category) {
            $qrCode->append(json_encode($category->toArray()));
            // You might want to add some separation between category data in the QR code.
            // You can append a newline or another separator to differentiate between categories.
            $qrCode->append(PHP_EOL); // For example, adding a newline between categories
        }
    
        // Return response with image
        return new Response($qrCode->writeString(), 200, [
            'Content-Type' => $qrCode->getContentType()
        ]);
    }}