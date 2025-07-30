<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\ProjectLogService;
use App\Form\ProjectType;
use App\Repository\DepartementRepository;
use App\Repository\ProjectRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    private ProjectLogService $projectLogService;

    public function __construct(ProjectLogService $projectLogService)
    {
        $this->projectLogService = $projectLogService;
    }

    #[Route('', name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, DepartementRepository $departementRepository,UserInterface $user): Response
    {
        $departementDeUser = $departementRepository->findOneBy(['chef'=>$user->getId()]);

        $projects = $projectRepository->findAll();
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

                if ($userId) {
                    if (!isset($progressionParUser[$userId])) {
                        $progressionParUser[$userId] = ['somme' => 0, 'count' => 0];
                    }
                    $progressionParUser[$userId]['somme'] += $progression;
                    $progressionParUser[$userId]['count']++;
                }

                $progressionParDepartement[$departementId]['somme'] += $progression;
                $progressionParDepartement[$departementId]['count']++;

                $totalSomme += $progression;
                $totalCount++;
            }
        }

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
            'nomdep'=>$nomdepartement,
            'couleur'=>$listeCouleur,
            'total_projet'=>count($projects),
            'total_encours'=>count($project_encours),
            'total_a_faire'=>count($project_a_faire),
            'total_bloque'=>count($project_bloque),
            'total_termine'=>count($project_termine),
            'departement_user'=>$departementDeUser,
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

            // Log la création
            $this->projectLogService->log($project, 'Création du projet');

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        $tasks = $project->getTasks();
        $total = count($tasks);
        $progressSum = 0;
    
        foreach ($tasks as $task) {
            $progressSum += $task->getProgression() ?? 0;
        }
    
        $averageProgress = $total > 0 ? (int)($progressSum / $total) : 0;
    
        // Récupération des logs pour ce projet
        $logs = $this->projectLogService->getLogsForProject($project);
    
        return $this->render('project/show.html.twig', [
            'project' => $project,
            'progressionGlobale' => $averageProgress,
            'logs' => $logs,
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Log la modification
            $this->projectLogService->log($project, 'Modification du projet');

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
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();

            // Log la suppression
            $this->projectLogService->log($project, 'Suppression du projet');
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export/csv', name: 'app_project_export_csv', methods: ['GET'])]
    public function exportCsv(ProjectRepository $projectRepository): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($projectRepository) {
            $handle = fopen('php://output', 'w');

            // Ajout du BOM UTF-8 pour éviter les problèmes d'encodage sous Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes du fichier CSV
            fputcsv($handle, ['ID', 'Titre', 'Département', 'Statut', 'Date de début', 'Deadline']);

            foreach ($projectRepository->findAll() as $project) {
                fputcsv($handle, [
                    $project->getId(),
                    $project->getTitre(),
                    $project->getDepartement()?->getNom() ?? '—',
                    $project->getStatut(),
                    $project->getDateDebut()?->format('Y-m-d'),
                    $project->getDateFin()?->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="projets.csv"');

        return $response;
    }
}
