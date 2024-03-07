<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Actualite;
use App\Entity\Commentaire;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Subscriber;
use Symfony\Component\Mime\Email;
use App\Form\NewsletterSubscriptionType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Bridge\Twig\Mime\TemplatedEmail; 


#[Route('/actualite')]
class ActualiteController extends AbstractController
{
    private $mailer;
    private $logger; // Declare the logger property

    public function __construct(MailerInterface $mailer, LoggerInterface $logger) // Inject LoggerInterface
    {
        $this->mailer = $mailer;
        $this->logger = $logger; // Assign the injected logger
    }

    #[Route('/', name: 'app_actualite')]
    public function index(): Response
    {
        return $this->render('actualite/index.html.twig');
    }


    #[Route('/afficher', name: 'app_afficher_actualite')]
    public function afficherActualite(ActualiteRepository $actualiteRepository, Request $request, FormFactoryInterface $formFactory): Response
    {
        $actualites = $actualiteRepository->findAll();
        
        // Create the subscription form
        $subscriber = new Subscriber();
        $form = $formFactory->create(NewsletterSubscriptionType::class, $subscriber);
        
        return $this->render('actualite/listesActualites.html.twig', [
            'actualites' => $actualites,
            'form' => $form->createView(),
        ]);
    }
    

    

    
    #[Route('/afficherB', name: 'app_afficher_actualitee')]
public function afficherActualitee(Request $request, ActualiteRepository $actualiteRepository): Response
{
    // Fetch actualites
    $actualites = $actualiteRepository->findAll();

    // Render the template with actualites
    return $this->render('actualite/listesActualitesB.html.twig', [
        'actualites' => $actualites,
    ]);
}

    
    #[Route('/afficherB/{titre}', name: 'app_reponse_index2', methods: ['GET','POST'])]
    public function indexrep(Request $request, ActualiteRepository $actualiteRepository, $titre): Response
    {
        // Fetch actualites filtered by title
        $actualites = $actualiteRepository->findByTitre($titre);
    
        return $this->render('actualite/listesActualitesB.html.twig', [
            'actualites' => $actualites,
            'titre' => $titre, // Provide the route parameter for app_reponse_index2
        ]);
    }
    
    #[Route('/ajouter', name: 'app_ajouter_actualite')]
    public function ajouterActualite(Request $request, EntityManagerInterface $em): Response
    {
        $actualite = new Actualite();
    
        if(!$actualite->getDatePublication()) {
            $actualite->setDatePublication(new \DateTime());
        }
    
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $file = $form['image_url']->getData();
            if ($file) {
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $file->move(
                        $this->getParameter('photos_directory'), // Define image directory in services.yaml
                        $fileName
                    );
                } catch (FileException $e) {
                    // Handle file upload error, if any
                }
    
                // Set the file name in the entity
                $actualite->setImageUrl($fileName);
            }
    
            // Persist and flush the entity
            $em->persist($actualite);
            $em->flush();
    
            // Send notification email to subscribers
            return $this->redirectToRoute('app_afficher_actualitee', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('actualite/ajouterActualite.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView()
        ]);
    }
 
    
    #[Route("/modifier/{id}", name: "app_modifier_actualite")]
    public function modifierActualite(Request $request, Actualite $actualite, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload only if a file is present
            if ($form['image_url']->getData()) {
                $file = $form['image_url']->getData();
                
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $file->move(
                        $this->getParameter('photos_directory'), // Define image directory in services.yaml
                        $fileName
                    );
                } catch (FileException $e) {
                    // Handle file upload error, if any
                }
    
                // Set the file name in the entity
                $actualite->setImageUrl($fileName);
            }
    
            $em->flush();
            return $this->redirectToRoute('app_afficher_actualitee');
        }
        
        return $this->render('actualite/modifierActualite.html.twig', [
            'form' => $form->createView()
        ]);
    }




    #[Route("/supprimer/{id<\d+>}", name: "app_supprimer_actualite")]
    public function supprimerActualite(Actualite $actualite, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('confirm', SubmitType::class, ['label' => 'Confirm'])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($actualite);
            $em->flush();
    
            return $this->redirectToRoute('app_afficher_actualitee');
        }
    
        return $this->render('actualite/confirmation_delete.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }
    

#[Route("/supprimer-commentaire/{id<\d+>}", name: "app_supprimer_commentaire")]
public function supprimerCommentaire(Commentaire $commentaire, EntityManagerInterface $em, Request $request): Response
{
    $actualiteId = $commentaire->getActualite()->getId();
    
    $form = $this->createFormBuilder()
        ->add('confirm', SubmitType::class, ['label' => 'Confirm Delete'])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->remove($commentaire);
        $em->flush();
        $this->addFlash('success', 'Commentaire supprimé avec succès.');
        return $this->redirectToRoute('app_afficher_actualitee', ['id' => $actualiteId]);
    }

    return $this->render('commentaire/confirm_delete.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form->createView(),
    ]);
}


#[Route("/supprimer-commentaire1/{id<\d+>}", name: "app_supprimer_commentaire1")]
public function supprimerCommentaire1(Commentaire $commentaire, EntityManagerInterface $em, Request $request): Response
{
    $actualiteId = $commentaire->getActualite()->getId();
    
    $form = $this->createFormBuilder()
        ->add('confirm', SubmitType::class, ['label' => 'Confirm Delete'])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->remove($commentaire);
        $em->flush();
        $this->addFlash('success', 'Commentaire supprimé avec succès.');
        return $this->redirect($request->getUri());
    }

    return $this->render('commentaire/confirm_delete1.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form->createView(),
    ]);
}

#[Route('/subscribe', name: 'subscribe')]
public function subscribe(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
{
    $form = $this->createForm(NewsletterSubscriptionType::class);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $subscriber = $form->getData(); // Retrieve the submitted subscriber object
        
        // Persist subscriber to the database
        $em->persist($subscriber);
        $em->flush();
        
        // Send welcome email to the subscriber
        $email = (new TemplatedEmail()) // Use TemplatedEmail instead of Email
            ->from('no-reply@nftun.com') // Replace with your email address
            ->to($subscriber->getEmail())
            ->subject('Welcome to our newsletter!')
            ->htmlTemplate('actualite/mailing.html.twig')
            ->context([
                'subscriber' => $subscriber, // Pass data to the template
            ]);
        $mailer->send($email);
        
        // Redirect to a suitable route after successful subscription
        return $this->redirectToRoute('app_afficher_actualite');
    }
    
    return $this->render('actualite/listesActualites.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/charts', name: 'app_chart_actualite')]
public function chartData(ActualiteRepository $actualiteRepository): Response
{
    
    
    $categories = [
        'Digital Art' => 0,
        'Collectibles' => 0,
        'Music' => 0,
        'Gaming' => 0,
        'Metaverse' => 0,
        'Sports' => 0,
        'Virtual Real Estate' => 0,
        'Crypto Art' => 0,
        'Fashion' => 0,
        'Domain Names' => 0,
        'NFT Platforms' => 0,
        'Memorabilia' => 0,
        'Other' => 0,
    ];

    $actualites = $actualiteRepository->findAll();

    foreach ($actualites as $actualite) {
        $category = $actualite->getCategorie();
        if (isset($categories[$category])) {
            $categories[$category]++;
        } else {
            $categories['Other']++;
        }
    }

    $data = [
        'labels' => array_keys($categories),
        'categoryCounts' => array_values($categories),
    ];

    // Encode the $data array to JSON format
    $chartDataJson = json_encode($data);

    // Render the Twig template with the JSON data
    return $this->render('actualite/charts.html.twig', [
        'chartDataJson' => $chartDataJson
    ]);
}


}
