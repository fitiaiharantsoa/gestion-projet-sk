<?php

namespace App\Controller;

use App\Repository\ProjectLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project/log')]
class ProjectLogController extends AbstractController
{
    #[Route('', name: 'app_project_log_index', methods: ['GET'])]
    public function index(ProjectLogRepository $projectLogRepository): Response
    {
        // Récupère tous les logs, éventuellement paginés ou triés
        $projectLogs = $projectLogRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('project_log/index.html.twig', [
            'project_logs' => $projectLogs,
        ]);
    }
}
