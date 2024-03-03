<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\NFT;
use App\Form\CartType;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index', methods: ['GET'])]
    public function index(Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $sessionId = $session->getId();

        $existingCart = $entityManager->getRepository(Cart::class)->findOneBy(['session_id' => $sessionId]);
        $nfts = $existingCart->getRelation();

        if ($existingCart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
            $entityManager->persist($cart);
            $entityManager->flush();
        }
    

        return $this->render('cart/show.html.twig', [
            'cart' => $existingCart,
            'nfts' => $nfts,
        ]);
    }


    #[Route('/new', name: 'app_cart_new', methods: ['GET'])] 
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $sessionId = $session->getId();
        $id = $request->query->get('id'); 

        $nft = $entityManager->getRepository(NFT::class)->find($id);
        if (!$nft) {
            return $this->redirectToRoute('error_page');
        }
    
        $existingCart = $entityManager->getRepository(Cart::class)->findOneBy(['session_id' => $sessionId]);
    
        if ($existingCart) {
            $existingCart->addRelation($nft);
            $cart = $existingCart;
        } else {
            $cart = new Cart();
            $cart->addRelation($nft);
            $cart->setSessionId($sessionId);
            $entityManager->persist($cart);
        }
    
        $entityManager->flush();
    
        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('', name: 'app_cart_deletenft', methods: ['GET','POST'])]
    public function delete(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {

        $session = $request->getSession();
        $sessionId = $session->getId();
        $id = $request->query->get('id'); 

        $nft = $entityManager->getRepository(NFT::class)->find($id);

        if (!$nft) {
            $this->addFlash('error', 'NFT not found.');
            return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
        }

        $existingCart = $entityManager->getRepository(Cart::class)->findOneBy(['session_id' => $sessionId]);
        if (!$existingCart) {
            $this->addFlash('error', 'Cart not found.');
            return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
        }

        $existingCart->removeRelation($nft); 
        $entityManager->persist($existingCart);
        $entityManager->flush();

        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
