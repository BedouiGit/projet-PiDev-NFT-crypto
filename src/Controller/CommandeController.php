<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\NFT;
use App\Entity\User;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\NFTRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Security;

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
        $commande->setDate(new \DateTime());
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

            return $this->redirectToRoute('afterlogin', [], Response::HTTP_SEE_OTHER);
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

        $commande->setDate(new \DateTime());

        $nft = $entityManager->getRepository(NFT::class)->find($id);

        $commande->setTotal($nft->getPrice());

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $nft->setUser($user);
        
        $commande->setUser($user);

        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()){
            $entityManager->persist($commande);
            $entityManager->flush();
            
            $nft->setCommande($commande);
            $nft->setStatus("private");
            $entityManager->persist($nft);
            $entityManager->flush();

            return $this->redirectToRoute('home_page', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/add.html.twig', [
            'nft' => $nft,
            'totalprice' => $nft->getPrice(),
            'commande' => $commande,
            'form' => $form->createView(),
            'user' => $user,
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


    #[Route('/commande/chart', name: 'commande_chart')]
    public function commandeChart(CommandeRepository $commandeRepository, EntityManagerInterface $entityManager, NFTRepository $NFTRepository): Response
    {
        $purchaseData = $commandeRepository->getTotalPurchasesPerDay();
        $topholders = $NFTRepository->getTopNFTOwners();
        
        $labels = [];
        $data = [];
        foreach ($purchaseData as $dayData) {
            $labels[] = $dayData['date'];
            $data[] = $dayData['totalPurchases'];
        }


        $holder = [];
        $Qte = [];

        foreach ($topholders as $tph) {
            

            if ($tph['userId'] === null) {
                $holder[] = null;
            } else {
                $user = new User();
                $user = $entityManager->getRepository(User::class)->find($tph['userId']);
                $holder[] = $user->getFirstName();
            }
            $Qte[] = $tph['nftCount'];
        }

        return $this->render('commande/stats.html.twig', [
            'labels' => $labels,
            'data' => $data,
            'holder' => $holder,
            'Qte' => $Qte,
        ]);
    }


}
