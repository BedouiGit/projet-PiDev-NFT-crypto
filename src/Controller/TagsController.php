<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Form\TagsType;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tags')]
class TagsController extends AbstractController
{
    #[Route('/', name: 'app_tags_index', methods: ['GET'])]
    public function index(TagsRepository $tagsRepository): Response
    {
        return $this->render('tags/index.html.twig', [
            'tags' => $tagsRepository->findAll(),
        ]);
    }
    

    #[Route('/new', name: 'app_tags_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $tag = new Tags();
    $form = $this->createForm(TagsType::class, $tag);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('phototag')->getData();

        if ($photoFile) {
            $newFilename = uniqid().'.'.$photoFile->guessExtension();
            $photoFile->move(
                $this->getParameter('photos_directory'),
                $newFilename
            );
            $tag->setPhototag($newFilename);
        }

        $entityManager->persist($tag);
        $entityManager->flush();

        return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('tags/new.html.twig', [
        'tag' => $tag,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_tags_show', methods: ['GET'])]
    public function show(Tags $tag): Response
    {
        return $this->render('tags/show.html.twig', [
            'tag' => $tag,
        ]);
    }
    #[Route('/{id}/front', name: 'app_tag_front_show', methods: ['GET'])]
    public function showfront(Tags $tag): Response
    {
        return $this->render('tags/showfront.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tags_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TagsType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tags/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tags_delete', methods: ['POST'])]
    public function delete(Request $request, Tags $tag, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tags_index', [], Response::HTTP_SEE_OTHER);
    }
}
