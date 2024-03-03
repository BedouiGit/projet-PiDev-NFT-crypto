<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Form\ProjetsType;
use App\Repository\ProjetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository; // Assuming you have a Category entity and corresponding repository

#[Route('/projets')]
class ProjetsController extends AbstractController
{
    #[Route('/category/{id}', name: 'app_projets_index', methods: ['GET'])]
    public function index(ProjetsRepository $projetsRepository , Request $request , int $id): Response
    {
        $categoryId = $request->attributes->get('id'); 
        $projets = $projetsRepository->findBy(['category' => $categoryId]);
        return $this->render('projets/index.html.twig', [
            'projets' => $projets,
            'categoryId' => $categoryId,
        ]);
    }

#[Route('/category/{categoryId}/new', name: 'app_projets_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, int $categoryId): Response
{
    $category = $categoryRepository->find($categoryId);

    if (!$category) {
        throw $this->createNotFoundException('The category does not exist');
    }

    $projet = new Projets();
    $projet->setCategory($category); // Assuming you have a setCategory method in your Projets entity

    if(!$projet->getDateDeCreation()){
        $projet->setDateDeCreation(new \DateTime());
    }

    $form = $this->createForm(ProjetsType::class, $projet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('photoURL')->getData();

        if ($photoFile) {
            $newFilename = uniqid().'.'.$photoFile->guessExtension();
            $photoFile->move(
                $this->getParameter('photos_directory'),
                $newFilename
            );
            $projet->setPhotoUrl($newFilename);
        }

        $entityManager->persist($projet);
        $entityManager->flush();

        return $this->redirectToRoute('app_projets_index', ['id' => $categoryId], Response::HTTP_SEE_OTHER);
    }

    return $this->render('projets/new.html.twig', [
        'projet' => $projet,
        'form' => $form->createView(),
            'categoryId' => $categoryId, // Make sure this line is included

    ]);
}

    #[Route('/{id}', name: 'app_projets_show', methods: ['GET'])]
    public function show(Projets $projet): Response
    {
        return $this->render('projets/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_projets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projets $projet, EntityManagerInterface $entityManager,int $id): Response
    {
        $form = $this->createForm(ProjetsType::class, $projet);
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
                $projet->setPhotoUrl($newFilename);
            }
            $entityManager->flush();
            // get the category id from the project
            $categoryId = $projet->getCategory()->getId();  

            return $this->redirectToRoute('app_projets_index', ['id' => $categoryId], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('projets/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
            'categoryId' => $id, //
        ]);
    }

    #[Route('/{id}', name: 'app_projets_delete', methods: ['POST'])]
    public function delete(Request $request, Projets $projet, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($projet);
            $entityManager->flush();
        $categoryId = $projet->getCategory()->getId(); 

        return $this->redirectToRoute('app_projets_index', ['id' => $categoryId], Response::HTTP_SEE_OTHER);
    }
}
