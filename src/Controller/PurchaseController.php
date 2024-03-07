<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    #[Route('/test', name: 'test_route')]
public function test(): Response
{
    return new Response('Test route works');
}


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

    #[Route('/about', name: 'about_page')]
    public function about(): Response
    {
        // Your logic here, for example, rendering a template
        return  $this->render('about.html.twig');
    }
    #[Route('/contact', name: 'contact_page')]
    public function contact(): Response
    {
        // Your logic here, for example, rendering a template
        return  $this->render('contact.html.twig');
    }
}
