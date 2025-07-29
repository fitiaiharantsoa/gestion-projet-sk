<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\DepartementRepository;
use App\Repository\ProjectRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    #[Route('', name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, DepartementRepository $departementRepository,UserInterface $user, Request $request): Response
    {
        $departementDeUser = $departementRepository->findOneBy(['chef'=>$this->getUser()]);
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $current_page = $page;
        $projectsReturn = $projectRepository->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
        $projects = $projectRepository->findAll();
        $totalProjects = $projectRepository->count([]);
        $totalPages = ceil($totalProjects / $limit);
        $project_encours = $projectRepository->findBy(['statut'=>'en cours']);
        $project_a_faire = $projectRepository->findBy(['statut'=> 'à faire']);
        $project_bloque = $projectRepository->findBy(['statut'=> 'bloqué']);
        $project_termine = $projectRepository->findBy(['statut'=> 'terminé']);
        $listeCouleur = [
            '#FFC1CF','#F13030','#5A0001','#22181C', '#F6E8EA', '#F45B69', '#2A2D43','#414361','#7F2CCB', '#FF84E8', '#FFA9E7'
        ];
        $progressionParUser = [];
        $progressionParDepartement = [];
        $nomdepartement = [];
        $totalSomme = 0;
        $totalCount = 0;

        foreach ($projects as $project) {
            $tasks = $project->getTasks();

            $departement = $project->getDepartement();
            $departementId = $departement ? $departement->getId() : null;
            $departementNom = $departement ? $departement->getNom() : null;

            if (!isset($progressionParDepartement[$departementId])) {
                $progressionParDepartement[$departementId] = ['somme' => 0, 'count' => 0];
                $nomdepartement[$departementId] = $departementNom;
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
            'projects' => $projectsReturn,
            'progressionParUser' => $progressionParUser,
            'progressionParDepartement' => $progressionParDepartement,
            'progressionGlobale' => $progressionGlobale,
            'nomdep'=>$nomdepartement,
            'couleur'=>$listeCouleur,
            'total_projet'=>count($projects),
            'total_encours'=>count($project_encours),
            'total_a_faire'=>count($project_a_faire),
            'total_bloque'=>count($project_bloque),
            'total_termine'=>count($project_termine),
            'departement_user'=>$departementDeUser,
            'current_page'=>$current_page,
            'total_pages'=>$totalPages,
            'total_projects'=>$totalProjects,
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, [
            'attr'=>['class'=>'container form-control']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime('now');
            $project->setStatut('à faire');
            $project->setCreatedAt($date);
            $project->setUpdatedAt($date);
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
