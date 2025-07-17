<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    #[Route('', name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        $progressionParUser = [];
        $progressionParDepartement = [];
        $totalSomme = 0;
        $totalCount = 0;

        foreach ($projects as $project) {
            $tasks = $project->getTasks();

            $departement = $project->getDepartement();
            $departementId = $departement ? $departement->getId() : null;

            if (!isset($progressionParDepartement[$departementId])) {
                $progressionParDepartement[$departementId] = ['somme' => 0, 'count' => 0];
            }

            foreach ($tasks as $task) {
                $user = $task->getAssigne();
                $userId = $user ? $user->getId() : null;
                $progression = $task->getProgression() ?? 0;

                // Par utilisateur
                if ($userId) {
                    if (!isset($progressionParUser[$userId])) {
                        $progressionParUser[$userId] = ['somme' => 0, 'count' => 0];
                    }
                    $progressionParUser[$userId]['somme'] += $progression;
                    $progressionParUser[$userId]['count']++;
                }

                // Par département
                $progressionParDepartement[$departementId]['somme'] += $progression;
                $progressionParDepartement[$departementId]['count']++;

                // Total global
                $totalSomme += $progression;
                $totalCount++;
            }
        }

        // Calcul des moyennes
        foreach ($progressionParUser as $userId => &$data) {
            $data = ($data['count'] > 0) ? round($data['somme'] / $data['count']) : 0;
        }
        foreach ($progressionParDepartement as $depId => &$data) {
            $data = ($data['count'] > 0) ? round($data['somme'] / $data['count']) : 0;
        }
        $progressionGlobale = ($totalCount > 0) ? round($totalSomme / $totalCount) : 0;

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'progressionParUser' => $progressionParUser,
            'progressionParDepartement' => $progressionParDepartement,
            'progressionGlobale' => $progressionGlobale,
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        // Calcul de la progression globale
        $tasks = $project->getTasks();
        $total = count($tasks);
        $progressSum = 0;

        foreach ($tasks as $task) {
            $progressSum += $task->getProgression() ?? 0;
        }

        $averageProgress = $total > 0 ? (int)($progressSum / $total) : 0;

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'progressionGlobale' => $averageProgress,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
