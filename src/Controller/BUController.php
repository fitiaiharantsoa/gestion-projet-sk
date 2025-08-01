<?php

namespace App\Controller;

use App\Entity\BU;
use App\Form\BUType;
use App\Repository\BURepository;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/bu')]
final class BUController extends AbstractController
{
    #[Route(name: 'app_b_u_index', methods: ['GET'])]
    public function index(BURepository $bURepository): Response
    {
        if (in_array('ROLE_BU', $this->getUser()->getRoles())) {
            return $this->render('bu/index.html.twig', [
                'b_us' => $bURepository->findBy(['responsable'=> $this->getUser()->getId()]),
            ]);
        }
        return $this->render('bu/index.html.twig', [
            'b_us' => $bURepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_b_u_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bU = new BU();
        $form = $this->createForm(BUType::class, $bU);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bU);
            $entityManager->flush();

            return $this->redirectToRoute('app_b_u_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bu/new.html.twig', [
            'b_u' => $bU,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_b_u_show', methods: ['GET'])]
    public function show(BU $bU, DepartementRepository $departementRepository): Response
    {
        $departements = $departementRepository->findBy(['bu' => $bU]);
        if (!$bU) {
            throw $this->createNotFoundException('BU not found');
        }
        return $this->render('bu/show.html.twig', [
            'b_u' => $bU,
            'departements'=>$departements
        ]);
    }

    #[Route('/{id}/edit', name: 'app_b_u_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BU $bU, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BUType::class, $bU);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_b_u_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bu/edit.html.twig', [
            'b_u' => $bU,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_b_u_delete', methods: ['POST'])]
    public function delete(Request $request, BU $bU, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bU->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bU);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_b_u_index', [], Response::HTTP_SEE_OTHER);
    }
}
