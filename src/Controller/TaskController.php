<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\ProjectLogService; // injection du service log
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    private ProjectLogService $projectLogService;

    public function __construct(ProjectLogService $projectLogService)
    {
        $this->projectLogService = $projectLogService;
    }

    #[Route('', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        if (in_array('ROLE_BU', $this->getUser()->getRoles())) {
            $task = $taskRepository->findBy(['createur'=>$this->getUser()->getId()], ['id'=>'DESC'], $limit, ($page - 1) * $limit);
            $totalTasks = $taskRepository->count(['createur' => $this->getUser()->getId()]);
            $totalPages = ceil($totalTasks/$limit);

            return $this->render('task/index.html.twig', [
                'tasks' => $task,
                'current_page'=>$page,
                'total_pages' => $totalPages,
                'total_task'=>$totalTasks
            ]);
        }
        
        $task = $taskRepository->findPaginatedTask($page, $limit);
        $totalTasks = $taskRepository->countAllTask();
        $totalPages = ceil($totalTasks / $limit);
        $currentPage = $page;


        return $this->render('task/index.html.twig', [
            'tasks' => $task,
            'current_page'=>$currentPage,
            'total_pages' => $totalPages,
            'total_task'=>$totalTasks
        ]);
    }

    #[Route('/my-tasks', name: 'app_task_list', methods: ['GET'])]
    public function myTasks(TaskRepository $taskRepository, Request $request): Response
    {
        $user = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos tâches.');
        }
        
        $tasks = $taskRepository->findBy(['assigne' => $user], ['id'=>"desc"], $limit, $offset);

        return $this->render('task/my_tasks.html.twig', [
            'tasks' => $tasks,
            'task_total'=> count($tasks),
            'current_page' => $page,
            'total_pages' => ceil(count($tasks) / $limit),
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $task->setCreateur($user);
            $task->setCreatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($task);
            $entityManager->flush();

            // Log création tâche (on log le projet lié si possible)
            if ($task->getProject()) {
                $this->projectLogService->log($task->getProject(), "Création de la tâche '{$task->getTitre()}'");
            }

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Log modification tâche
            if ($task->getProject()) {
                $this->projectLogService->log($task->getProject(), "Modification de la tâche '{$task->getTitre()}'");
            }

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $project = $task->getProject(); // récupérer le projet avant suppression

            $entityManager->remove($task);
            $entityManager->flush();

            if ($project) {
                $this->projectLogService->log($project, "Suppression de la tâche '{$task->getTitre()}'");
            }
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/start', name: 'app_task_start', methods: ['GET'])]
    public function startTask(Task $task, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($task->getAssigne() !== $user) {
            throw $this->createAccessDeniedException('Ce n\'est pas votre tâche.');
        }

        $task->setStatut('en cours');
        $em->flush();

        if ($task->getProject()) {
            $this->projectLogService->log($task->getProject(), "Démarrage de la tâche '{$task->getTitre()}' par {$user->getUserIdentifier()}");
        }

        return $this->redirectToRoute('app_task_list');
    }

    #[Route('/{id}/finish', name: 'app_task_finish', methods: ['GET'])]
    public function finishTask(Task $task, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($task->getAssigne() !== $user) {
            throw $this->createAccessDeniedException('Ce n\'est pas votre tâche.');
        }

        $task->setStatut('terminée');
        $task->setProgression(100);
        $em->flush();

        if ($task->getProject()) {
            $this->projectLogService->log($task->getProject(), "Terminaison de la tâche '{$task->getTitre()}' par {$user->getUserIdentifier()}");
        }

        return $this->redirectToRoute('app_task_list');
    }

    /**
     * MÉTHODE MODIFIÉE : Ajout des permissions et gestion de la progression
     */
    #[Route('/{id}/update-status', name: 'app_task_update_status', methods: ['POST'])]
    public function updateStatus(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json(['error' => 'Tâche non trouvée'], 404);
        }

        $currentUser = $this->getUser();
        
        // AJOUT : Vérification des permissions
        $canModify = false;
        
        if ($this->isGranted('ROLE_PDG') || $this->isGranted('ROLE_CHEF_DEPARTEMENT')) {
            // PDG et Chef de département peuvent modifier toutes les tâches
            $canModify = true;
        } elseif ($task->getAssigne() === $currentUser) {
            // Collaborateur peut modifier seulement ses propres tâches
            $canModify = true;
        }

        if (!$canModify) {
            return $this->json(['error' => 'Vous ne pouvez modifier que vos propres tâches'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['status'])) {
            return $this->json(['error' => 'Statut manquant'], 400);
        }

        $newStatus = $data['status'];

        $validStatuses = ['à faire', 'en cours', 'bloquée', 'terminée'];
        if (!in_array($newStatus, $validStatuses)) {
            return $this->json(['error' => 'Statut invalide'], 400);
        }

        $task->setStatut($newStatus);

        // AJOUT : Mise à jour automatique de la progression selon le statut
        switch ($newStatus) {
            case 'à faire':
                $task->setProgression(0);
                break;
            case 'en cours':
                if ($task->getProgression() === null || $task->getProgression() === 0) {
                    $task->setProgression(25);
                }
                break;
            case 'terminée':
                $task->setProgression(100);
                break;
            // 'bloquée' garde sa progression actuelle
        }

        $em->flush();

        if ($task->getProject()) {
            $userName = $currentUser ? $currentUser->getUserIdentifier() : 'Utilisateur inconnu';
            $this->projectLogService->log($task->getProject(), "Mise à jour du statut de la tâche '{$task->getTitre()}' à '{$newStatus}' par {$userName}");
        }

        // AJOUT : Retourner les données de la tâche mise à jour pour le JavaScript
        return $this->json([
            'success' => true,
            'task' => [
                'id' => $task->getId(),
                'statut' => $task->getStatut(),
                'progression' => $task->getProgression(),
            ],
        ]);
    }
}