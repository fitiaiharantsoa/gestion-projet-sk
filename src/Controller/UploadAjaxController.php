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
        
        // Debug temporaire - À SUPPRIMER après résolution
        $tmpDir = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
        error_log("Upload tmp dir: " . $tmpDir);
        error_log("Tmp dir writable: " . (is_writable($tmpDir) ? 'YES' : 'NO'));
        
        // Créer le dossier tmp s'il n'existe pas
        if (!is_dir('C:\xampp2\tmp')) {
            mkdir('C:\xampp2\tmp', 0777, true);
        }
        
        // SOLUTION TEMPORAIRE : Copier le fichier manuellement si nécessaire
        if ($file->isValid()) {
            $tempPath = $file->getPathname();
            
            // Si le fichier temporaire n'est pas accessible, essayons une autre approche
            if (!is_readable($tempPath)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Fichier temporaire non accessible. Vérifiez la configuration PHP upload_tmp_dir dans php.ini'
                ]);
            }
        }
        
        if (!$file) {
            return $this->json([
                'success' => false,
                'message' => 'Aucun fichier reçu'
            ]);
        }

        if (!$file->isValid()) {
            $error = $file->getError();
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'Le fichier est trop volumineux (limite PHP)',
                UPLOAD_ERR_FORM_SIZE => 'Le fichier est trop volumineux (limite formulaire)',
                UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement uploadé',
                UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été uploadé',
                UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
                UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier sur le disque',
                UPLOAD_ERR_EXTENSION => 'Upload arrêté par une extension PHP'
            ];
            
            $message = $errorMessages[$error] ?? 'Erreur d\'upload inconnue (code: ' . $error . ')';
            
            return $this->json([
                'success' => false,
                'message' => 'Fichier invalide: ' . $message
            ]);
        }

        // Vérifier que le fichier temporaire existe
        if (!is_readable($file->getPathname())) {
            return $this->json([
                'success' => false,
                'message' => 'Fichier temporaire non accessible: ' . $file->getPathname()
            ]);
        }

        // Vérifier qu'un projet est sélectionné
        if (!$projectId) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez sélectionner un projet'
            ]);
        }

        // Vérifier que le projet existe
        $project = $projectRepository->find($projectId);
        if (!$project) {
            return $this->json([
                'success' => false,
                'message' => 'Projet non trouvé'
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
            $projectFile->setFilename($file->getClientOriginalName()); // Nom original
            $projectFile->setType($file->getMimeType());
            $projectFile->setDateUpload(new \DateTimeImmutable());
            $projectFile->setProject($project);

            // SUPPRIMÉ: dd($projectFile); - Cette ligne bloquait tout !

            $em->persist($projectFile);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Fichier uploadé avec succès !',
                'data' => [
                    'id' => $projectFile->getId(),
                    'filename' => $newFilename,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'project' => $project->getTitre()
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