<?php

namespace App\Controller;

use App\Entity\NFT;
use App\Form\NFTType;
use App\Repository\NFTRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nft')]
class NFTController extends AbstractController
{
    #[Route('/show', name: 'app_nft_show', methods: ['GET'])]
    public function show(NFTRepository $nFTRepository): Response
    {
        return $this->render('nft/show.html.twig', [
            'nfts' => $nFTRepository->findAll(),
        ]);
    }

    #[Route('/usershow', name: 'app_nft_usershow', methods: ['GET'])]
    public function usershow(NFTRepository $nFTRepository): Response
    {
        return $this->render('nft/usershow.html.twig', [
            'nfts' => $nFTRepository->findAll(),
        ]);
    }

    #[Route('/showback', name: 'app_nft_showback', methods: ['GET'])]
    public function showback(NFTRepository $nFTRepository): Response
    {
        return $this->render('nft/showback.html.twig', [
            'nfts' => $nFTRepository->findAll(),
        ]);
    }

    #[Route('/list/{id}', name: 'app_nft_index', methods: ['GET'])]
    public function index(NFTRepository $nFTRepository, int $id): Response
    {
        return $this->render('nft/index.html.twig', [
            'id' => $id,
            'nfts' => $nFTRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_nft_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nFT = new NFT();
        if (!$nFT->getCreationDate()) {
            $nFT->setCreationDate(new \DateTime());
        }
        $form = $this->createForm(NFTType::class, $nFT);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle photo upload
            $photoFile = $form->get('image')->getData();
            if ($photoFile) {
                // Generate a unique filename
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
    
                // Move the file to the directory where photos are stored
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
    
                // Set the photo URL in the Category entity
                $nFT->setImage($newFilename);

            $entityManager->persist($nFT);
            $entityManager->flush();

            return $this->redirectToRoute('app_nft_show', [], Response::HTTP_SEE_OTHER);
        }}

        return $this->render('nft/new.html.twig', [
            'nft' => $nFT,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}/edit', name: 'app_nft_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NFT $nFT, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NFTType::class, $nFT);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoFile = $form->get('image')->getData();
            if ($photoFile) {
                // Générez un nom de fichier unique
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                // Déplacez le fichier vers le répertoire où sont stockées les photos
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                // Mettez à jour l'URL de l'image dans l'entité Project
                $nFT->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_nft_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nft/edit.html.twig', [
            'nft' => $nFT,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_nft_delete', methods: ['POST'])]
    public function delete(Request $request, NFT $nFT, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nFT->getId(), $request->request->get('_token'))) {
        $commandes = $nFT->getCommande();
        if ($commandes !== null) {
            foreach($commandes as $commande) {
                $entityManager->remove($commande);
            }
        }
        
            $entityManager->remove($nFT);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nft_show', [], Response::HTTP_SEE_OTHER);
    }




}