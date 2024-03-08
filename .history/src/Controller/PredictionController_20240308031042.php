<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PredictionController extends AbstractController
{
  x

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/predict', name: 'predict', methods: ['POST'])]
    public function predict(Request $request, CommandeRepository $commandeRepository): JsonResponse
    {
        $commandes = $commandeRepository->getRecentCommandes();

        $formattedData = array_map(function ($commande) {
        return [
            'date' => $commande->getDate()->format('Y-m-d'), 
            'total' => $commande->getTotal(), 
        ];
        }, $commandes);

        $response = $this->httpClient->request('POST', 'http://192.168.1.134:5000/predict', [
            'json' => $formattedData,
        ]);

        $predictions = $response->toArray();

        return new JsonResponse($predictions);
    }

    #[Route('/predictions', name: 'predictions_view')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->getRecentCommandes();

        $formattedData = array_map(function ($commande) {
        return [
            'date' => $commande->getDate()->format('Y-m-d'), 
            'total' => $commande->getTotal(), 
        ];
        }, $commandes);

        
        return $this->render('prediction/index.html.twig',['data' => $formattedData]);
    }
}