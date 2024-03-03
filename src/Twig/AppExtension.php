<?php


namespace App\Twig;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AppExtension extends AbstractExtension
{
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;


    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCartCount', [$this, 'getCartCount']),
        ];
    }

    public function getCartCount(): int
    {
        $session = $this->requestStack->getSession();
        $sessionId = $session->getId();



        $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['session_id' => $sessionId]);

        if (!$cart) {
            return 0;
        }

        return count($cart->getRelation());
    }
}