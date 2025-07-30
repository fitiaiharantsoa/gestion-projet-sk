<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectFile;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadController extends AbstractController
{
    #[Route('/upload/ajax', name: 'app_upload_ajax', methods: ['POST'])]
    public function uploadAjax(
        Request $request,
        EntityManagerInterface $em,
        ProjectRepository $projectRepository
    ): JsonResponse {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('fichier');
        $projectId = $request->request->get('project_id');

        if (!$file || !$projectId) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Fichier ou projet manquant.'
            ], 400);
        }

        $project = $projectRepository->find($projectId);

        if (!$project) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Projet introuvable.'
            ], 404);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getParameter('uploads_directory'), $newFilename);

            $projectFile = new ProjectFile();
            $projectFile->setFilename($newFilename);
            $projectFile->setProject($project);
            $projectFile->setUploadedAt(new \DateTimeImmutable());

            $em->persist($projectFile);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Fichier envoyé avec succès !'
            ]);
        } catch (FileException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du fichier.'
            ], 500);
        }
    }

    #[Route('/upload/form', name: 'app_upload_form')]
    public function showForm(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->render('upload/upload_form.html.twig', [
            'projects' => $projects
        ]);
    }
}
