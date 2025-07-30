<?php

namespace App\Controller;

use App\Entity\ProjectFile;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadAjaxController extends AbstractController
{
    #[Route('/upload', name: 'app_upload_form')]
    public function form(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        return $this->render('upload/index.html.twig', ['projects' => $projects]);
    }

    #[Route('/upload-ajax', name: 'app_upload_ajax', methods: ['POST'])]
    public function uploadAjax(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em,
        ProjectRepository $projectRepository
    ): JsonResponse {
        
        $file = $request->files->get('fichier');
        $projectId = $request->request->get('project_id');
        
        if (!$file) {
            return $this->json([
                'success' => false,
                'message' => 'Aucun fichier reçu'
            ]);
        }

        if (!$file->isValid()) {
            return $this->json([
                'success' => false,
                'message' => 'Fichier invalide'
            ]);
        }

        // Génération du nom de fichier sécurisé
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Dossier d'upload
        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        try {
            // Déplacer le fichier
            $file->move($uploadDir, $newFilename);
            
            // Sauvegarder en base de données
            $projectFile = new ProjectFile();
            $projectFile->setUrl($newFilename);
            $projectFile->setType($file->getMimeType());
            $projectFile->setDateUpload(new \DateTimeImmutable());

            // Associer à un projet si fourni
            if ($projectId) {
                $project = $projectRepository->find($projectId);
                if ($project) {
                    $projectFile->setProject($project);
                }
            }

            $em->persist($projectFile);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Fichier uploadé avec succès !',
                'data' => [
                    'filename' => $newFilename,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload : ' . $e->getMessage()
            ]);
        }
    }
}