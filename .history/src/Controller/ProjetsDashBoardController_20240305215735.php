<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Form\Projets1Type;
use App\Repository\ProjetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

#[Route('/projets/dash/board')]
class ProjetsDashBoardController extends AbstractController
{
    #[Route('/', name: 'app_projets_dash_board_index', methods: ['GET'])]
    public function index(ProjetsRepository $projetsRepository): Response
    {
        return $this->render('projets_dash_board/index.html.twig', [
            'projets' => $projetsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_projets_dash_board_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projets();
        $projet->setDateDeCreation(new \DateTime());
        $form = $this->createForm(Projets1Type::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoURL')->getData();

            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $projet->setPhotoUrl($newFilename);
            }
            $entityManager->persist($projet);
            $entityManager->flush();

            return $this->redirectToRoute('app_dash_board_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projets_dash_board/new.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_projets_dash_board_show', methods: ['GET'])]
    public function show(Projets $projet): Response
    {
        return $this->render('projets_dash_board/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_projets_dash_board_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projets $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Projets1Type::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoURL')->getData();

            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $projet->setPhotoUrl($newFilename);
            }

            $entityManager->persist($projet);
            $entityManager->flush();

            return $this->redirectToRoute('app_dash_board_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projets_dash_board/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_projets_dash_board_delete', methods: ['POST'])]
    public function delete(Request $request, Projets $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projets_dash_board_index', [], Response::HTTP_SEE_OTHER);
    }
}
