<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    #[Route('/', name: 'home_page')]
    public function index(): Response
    {
        // Your logic here, for example, rendering a template
        return  $this->render('home.html.twig');
    }
    #[Route('/purchase', name: 'app_purchase')]
    public function buy(): Response
    {
        return $this->render('purchase/index.html.twig');
    }
}
