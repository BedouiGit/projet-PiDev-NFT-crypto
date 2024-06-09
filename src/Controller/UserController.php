<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\NFTRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/exportpdf', name: 'app_generer_pdf_historique')]
    public function exportPdf(UserRepository $userRepository): Response
    {

    $users = $userRepository->findAll();

    // Créez une instance de Dompdf avec les options nécessaires
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($pdfOptions);

    // Générez le HTML pour représenter la table d'utilisateurs
    $html = $this->renderView('admin/pdf.html.twig', ['users' => $users]);

    // Chargez le HTML dans Dompdf et générez le PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Générer un nom de fichier pour le PDF
    $filename = 'user_list.pdf';

    // Streamer le PDF vers le navigateur
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

    // Retournez la réponse
    return $response;
    }


    #[Route('/chart', name: 'app_chart_user')]

    public function userChart(UserRepository $repo): Response
    {
        // Get user data by age or address
        $userData = $repo->getUsersByAge(); // or $repo->getUsersByAddress('your_address');

        $labels = [];
        $data = [];
        foreach ($userData as $groupData) {
            $labels[] = $groupData['age']; // or $groupData['address'];
            $data[] = $groupData['userCount'];
        }

        return $this->render('admin/stat.html.twig', [
            'labels' => $labels,
            'data' => $data,
            'chartType' => 'user_age' 
        ]);
    }


    #[Route('/afterlogin', name: 'afterlogin')]
    public function test(UserRepository $repo, Security $security, NFTRepository $nFTRepository): Response
    {
        
        if ($security->isGranted('ROLE_ADMIN')) 
        {
            return $this->redirectToRoute('admin_dash');
        }
        if ($security->isGranted('ROLE_USER')) 
        {
            $nfts = $nFTRepository->findAll();

            return $this->render('auth/author.html.twig', [
                'nfts' =>  $nfts,
            ]);
        }
        return $this->redirectToRoute('app_login');
    }

    #[Route('/admin', name: 'admin_dash')]
    public function admin(UserRepository $repo): Response
    {
        $Users = $repo->findAll();
        return $this->render('admin/admin.html.twig', [
            'users' => $Users,
        ]);
    }

    #[Route('/auth', name: 'auth_front')]
    public function auth(NFTRepository $nFTRepository): Response
    {
        $nfts = $nFTRepository->findAll();
    
        return $this->render('auth/author.html.twig', [
            'nfts' => $nfts
        ]);
    }

     #[Route('/editprofile', name: 'edit_profile')]
     public function edit_profile(UserRepository $repo): Response
    {
             return $this->render('auth/edit-profile.html.twig')
         ;
    }
    
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager ): Response
    {

        $user = new User();
        $user = $entityManager->getRepository(User::class)->find($id);

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, $id): Response
    {

        $user = new User();
        $user = $entityManager->getRepository(User::class)->find($id);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
           

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/editAuth', name: 'edit_auth', methods: ['GET', 'POST'])]
    public function edit_User(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $user = new User();
        $user = $entityManager->getRepository(User::class)->find($id);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('auth_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit_auth.html.twig', [
            'registrationForm' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        $user = $entityManager->getRepository(User::class)->find($id);


        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/User/Delete/{id}', name: 'Delete_User')]
    public function DeleteUser(ManagerRegistry $doctrine, $id): Response 
    {
        $em= $doctrine->getManager();
        $repo= $doctrine->getRepository(User::class);
        $User= $repo->find($id);
        $em->remove($User);
        $em->flush();

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/admin/user/block/{userId}', name: 'app_admin_block_user')]
    public function blockUser(int $userId,EntityManagerInterface $entityManager, UserRepository $repo ) : Response
    {
        $user = $repo->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }


        $user->setisBanned(true);
        $entityManager->flush();
        $this->addFlash('success', 'User blocked successfully.');

        return $this->redirectToRoute('app_user_index'); // Redirect to the admin page
    }

    #[Route('/admin/user/unblock/{userId}', name: 'app_admin_unblock_user')]
    public function unblockUser(int $userId,EntityManagerInterface $entityManager,  UserRepository $repo) : Response
    {
        $user = $repo->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        $user->setisBanned(false);
        $entityManager->flush();
        $this->addFlash('success', 'User blocked successfully.');

        return $this->redirectToRoute('app_user_index');
    }
    

}
