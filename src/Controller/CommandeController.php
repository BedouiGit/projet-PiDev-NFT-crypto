<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\NFT;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;


class CommandeController extends AbstractController
{
    #[Route('/checkout', name: 'app_handle_checkout', methods: ['GET', 'POST'])]
    public function checkout(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();

        

        $nftIds = $request->query->get('nftIds', []);
        $nfts = [];
        $totalPrice = 0.0;
    
            $nfts  = $entityManager->getRepository(NFT::class)->findBy(['id' => $nftIds]);
            foreach ($nfts as $nft){
                $totalPrice += $nft->getPrice();
        }
    
        $commande->setTotal($nft->getPrice());
        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isvalid()){
            $entityManager->persist($commande);
            $entityManager->flush();
            foreach ($nfts as $nft){
            $nft->setCommande($commande);
            $nft->setStatus("private");
            $entityManager->persist($nft);
        }
            $entityManager->flush();

            return $this->redirectToRoute('app_nft_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/checkout.html.twig', [
            'total' => $totalPrice,
            'nfts' => $nfts,
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/add', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $id = $request->query->get('id');
        $commande = new Commande();

        $nft = $entityManager->getRepository(NFT::class)->find($id);

        $commande->setTotal($nft->getPrice());
        

        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()){
            $entityManager->persist($commande);
            $entityManager->flush();
            
            $nft->setCommande($commande);
            $nft->setStatus("private");
            $entityManager->persist($nft);
            $entityManager->flush();

            return $this->redirectToRoute('app_nft_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/add.html.twig', [
            'nft' => $nft,
            'totalprice' => $nft->getPrice(),
            'commande' => $commande,
            'form' => $form->createView(),
        ]);

    }


    #[Route('/commande/show', name: 'app_Commande', methods: ['GET'])]
    public function show(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/Commande.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }


    #[Route('/commande/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->query->get('id');
        $commande = $entityManager->getRepository(Commande::class)->find($id);
    
        if (!$commande) {
            throw $this->createNotFoundException('No commande found for id '.$id);
        }
    
        $nfts = $commande->getNfts();
        $total = 0;
        foreach ($nfts as $nft) {
            $total += $nft->getPrice();
        }
    
        $commande->setTotal($total);
    
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request); // Make sure to handle the request
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_Commande', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('commande/edit.html.twig', [
            'nfts' => $nfts, // corrected variable name to match the template variable
            'commande' => $commande,
            'form' => $form->createView(), // Ensure you call createView() when passing a form to Twig
        ]);
    }
    
    

    #[Route('commande/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $cmd, EntityManagerInterface $entityManager): Response
    {
        $nfts = $entityManager->getRepository(NFT::class)->findBy(['commande' => $cmd]);

        foreach ($nfts as $nft) {
            $nft->setCommande(null);
            $entityManager->persist($nft);
        }

        if ($this->isCsrfTokenValid('delete'.$cmd->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cmd);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_Commande', [], Response::HTTP_SEE_OTHER);
    }
}
