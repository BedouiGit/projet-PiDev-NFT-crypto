<?php

namespace App\Controller;

use App\Entity\User;
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
use Dompdf\Dompdf;

use Dompdf\Options;


#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/afterlogin', name: 'afterlogin')]
    public function test(UserRepository $repo, Security $security): Response
    {
        
        if ($security->isGranted('ROLE_ADMIN')) 
        {
            return $this->redirectToRoute('admin_dash');
        }
        if ($security->isGranted('ROLE_USER')) 
        {
            return $this->render('auth/author.html.twig');
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
    public function auth(UserRepository $repo): Response
    {
            return $this->render('auth/auth.html.twig');
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
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

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
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


    #[Route('/export-pdf', name: 'app_generer_pdf_historique')]
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
}
