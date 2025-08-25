<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/departement')]
final class DepartementController extends AbstractController
{
    #[Route('/', name: 'app_departement_index', methods: ['GET'])]
    public function index(DepartementRepository $departementRepository, ProjectRepository $projectRepository, Request $request, UserRepository $userRepository): Response
    {
        $page = $request->query->getInt('page',1);
        $limit= 10;
        $offset = ($page - 1) * $limit;


        if (in_array('ROLE_BU', $this->getUser()->getRoles())) {
            $departements = $departementRepository->findBy(['chef'=> $this->getUser()->getId()], ['nom' => 'ASC'], $limit, $offset);
        }else{
            $departements = $departementRepository->findBy([], ['nom' => 'ASC'], $limit, $offset);
        }
        $listeProjetParDepartement = [];
        $listeMembreDepartement = [];
        foreach ($departements as $key => $value) {
            $projet = $projectRepository->findBy(['departement'=>$value->getId()]);
            $users = $userRepository->findBy(['departement' => $value->getId()]);
            if (!empty($projet)) {
                $listeProjetParDepartement[$value->getId()] = count($projet);
                $listeMembreDepartement[$value->getId()] = count($users);
            }else{
                $listeProjetParDepartement[$value->getId()] = 0;
                $listeMembreDepartement[$value->getId()] = 0;
            }
        }
        return $this->render('departement/index.html.twig', [
            'departements' => $departements,
            'nbr_projet'=>$listeProjetParDepartement,
            'nbr_membre'=>$listeMembreDepartement,
            'current_page' =>$page,
            'total_departements' => count($departements),
            'total_pages' => ceil(count($departements) / $limit),
        ]);
    }

    #[Route('/new', name: 'app_departement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($departement);
            $entityManager->flush();

            return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('departement/new.html.twig', [
            'departement' => $departement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_departement_show', methods: ['GET'])]
    public function show(Departement $departement, ProjectRepository $projectRepository, UserRepository $userRepository): Response
    {
        $projets = $projectRepository->findBy(['departement' => $departement->getId()]);
        $membres = $userRepository->findBy(['departement' => $departement->getId()]);
        return $this->render('departement/show.html.twig', [
            'departement' => $departement,
            'projet'=>$projets,
            'membres'=>$membres,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_departement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Departement $departement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('departement/edit.html.twig', [
            'departement' => $departement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_departement_delete', methods: ['POST'])]
    public function delete(Request $request, Departement $departement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($departement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
    }
}
