<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Entity\Commentaire;
use App\Entity\Subscriber;
use App\Form\CommentaireType;
use App\Form\NewsletterSubscriptionType;
use App\Repository\ActualiteRepository;
use App\Repository\CommentaireRepository;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route as AnnotationRoute;

#[AnnotationRoute('/commentaire')]
class CommentaireController extends AbstractController
{
    #[AnnotationRoute('/', name: 'app_commentaire')]
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }
    
  
    #[AnnotationRoute('/afficher/{id}', name: 'app_afficher_com')]
    public function afficherActualiteEtCommentaires(
        $id, 
        ActualiteRepository $actualiteRepository, 
        CommentaireRepository $commentaireRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $actualite = $actualiteRepository->find($id);

        if (!$actualite) {
            throw $this->createNotFoundException('Actualite not found');
        }

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setActualite($actualite);
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_afficher_com', ['id' => $id]);
        }

        $commentaires = $commentaireRepository->findBy(['actualite' => $actualite]);

        return $this->render('commentaire/listesCommentaires.html.twig', [
            'actualites' => $actualite,
            'commentaires' => $commentaires,
            'form' => $form->createView()
        ]);
    }

    

    #[AnnotationRoute('/afficherB/{id}', name: 'app_afficher_comB')]
    public function afficherCommentairesB($id, ActualiteRepository $actualiteRepository, CommentaireRepository $commentaireRepository): Response
    {
        $actualite = $actualiteRepository->find($id);
    
        if (!$actualite) {
            throw $this->createNotFoundException('Actualite not found');
        }
    
        $commentaires = $commentaireRepository->findBy(['actualite' => $actualite]);
    
        return $this->render('commentaire/listesCommentaireB.html.twig', [
            'actualite' => $actualite,
            'commentaires' => $commentaires,
        ]);
    }

    
    

    #[AnnotationRoute('/ajouter-commentaire', name: 'app_ajouter_commentaire')]
    public function ajouterCommentaire(Request $request, EntityManagerInterface $em): Response
    {
        $commentaire = new Commentaire(); 
        $commentaire->setDateContenu(new \DateTime()); // Set the date_contenu field to the current date and time
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em->persist($commentaire);
            $em->flush();
            
            return $this->redirectToRoute('app_afficher_commentaire_b');
        }
        
        return $this->render('commentaire/ajouterCommentaire.html.twig', [
            'form' => $form->createView()
        ]);

    }


    

    }



  
    