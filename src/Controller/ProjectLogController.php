<?php

namespace App\Controller;

use App\Entity\ProjectLog;
use App\Form\ProjectLog1Type;
use App\Repository\ProjectLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project/log')]
final class ProjectLogController extends AbstractController
{
    #[Route(name: 'app_project_log_index', methods: ['GET'])]
    public function index(ProjectLogRepository $projectLogRepository): Response
    {
        return $this->render('project_log/index.html.twig', [
            'project_logs' => $projectLogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_project_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projectLog = new ProjectLog();
        $form = $this->createForm(ProjectLog1Type::class, $projectLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projectLog);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_log/new.html.twig', [
            'project_log' => $projectLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_log_show', methods: ['GET'])]
    public function show(ProjectLog $projectLog): Response
    {
        return $this->render('project_log/show.html.twig', [
            'project_log' => $projectLog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProjectLog $projectLog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectLog1Type::class, $projectLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_log/edit.html.twig', [
            'project_log' => $projectLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_log_delete', methods: ['POST'])]
    public function delete(Request $request, ProjectLog $projectLog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectLog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projectLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_log_index', [], Response::HTTP_SEE_OTHER);
    }
}
