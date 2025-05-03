<?php

namespace App\Controller;

use App\Entity\PoliceAssurance;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PoliceAssuranceRepository;
use App\Repository\CompagnieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class PoliceAssuranceController extends AbstractController
{
    #[Route('/api/policeAssurance/insert', name: 'app_police_assurance_insert', methods: ['POST'])]

    public function insert(EntityManagerInterface $entity_manager,CompagnieRepository $compagnie_repository, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        try {
            $policeAssurance = new PoliceAssurance();
            $policeAssurance->setProprietaireAssurance($data['proprietaireAssurance']);
            $policeAssurance->setBeneficaireAssurance($data['beneficiaireAssurance']);
            $compagnie = $compagnie_repository->find($data['compagnieId']);
            if (!$compagnie) {
                return $this->json([
                    'status' => 404,
                    'message' => 'Compagnie not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $policeAssurance->setCompagnie($compagnie);
            $entity_manager->persist($policeAssurance);
            $entity_manager->flush();

            return $this->json(
                [
                    'status' => 200,
                    'message' => 'Police d\'assurance ajoutee avec succes'
                ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de l\'insertion de la police d\'assurance'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/api/policeAssurance/getAll', name: 'app_police_assurance_getAll', methods: ['GET'])]
public function getAll(PoliceAssuranceRepository $policeAssuranceRepository): Response
{
    try {
        $policeAssurances = $policeAssuranceRepository->findAll();
        
        $result = [];
        foreach ($policeAssurances as $policeAssurance) {
            $result[] = [
                'id' => $policeAssurance->getId(),
                'proprietaireAssurance' => $policeAssurance->getProprietaireAssurance(),
                'beneficaireAssurance' => $policeAssurance->getBeneficaireAssurance(),
                'compagnie' => [
                    'id' => $policeAssurance->getCompagnie()->getId(),
                    'nom' => $policeAssurance->getCompagnie()->getNomCompagnie() 
                ]

            ];
        }
        
        return $this->json(
            [
                'status' => 200,
                'message' => 'Polices d\'assurance récupérées avec succès',
                'data' => $result
            ], 
            Response::HTTP_OK
        );
    } catch (\Exception $e) {
        return $this->json([
            'status' => 500,
            'message' => 'Erreur lors de la récupération des polices d\'assurance: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

#[Route('api/policeAssurance/get/{id}', name: 'app_police_assurance_get', methods: ['GET'])]
public function get(PoliceAssuranceRepository $policeAssuranceRepository, int $id): Response
{
    try {
        $policeAssurance = $policeAssuranceRepository->find($id);
        
        if (!$policeAssurance) {
            return $this->json([
                'status' => 404,
                'message' => 'Police d\'assurance non trouvée'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Conversion de l'entité en tableau pour éviter les problèmes de sérialisation
        $policeData = [
            'id' => $policeAssurance->getId(),
            'proprietaireAssurance' => $policeAssurance->getProprietaireAssurance(),
            'beneficaireAssurance' => $policeAssurance->getBeneficaireAssurance(),
            // Gestion de la relation avec la compagnie
            'compagnie' => [
                'id' => $policeAssurance->getCompagnie()->getId(),
                'nom' => $policeAssurance->getCompagnie()->getNomCompagnie()
                // Ajoutez d'autres attributs de compagnie si nécessaire
            ]
            // Ajoutez d'autres propriétés de la police d'assurance si nécessaire
        ];
        
        return $this->json([
            'status' => 200,
            'message' => 'Police d\'assurance récupérée avec succès',
            'data' => $policeData
        ], Response::HTTP_OK);
        
    } catch (\Exception $e) {
        return $this->json([
            'status' => 500,
            'message' => 'Erreur lors de la récupération de la police d\'assurance: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
    
    #[Route('api/policeAssurance/update/{id}', name: 'app_police_assurance_update', methods: ['PATCH'])]    
    public function update(EntityManagerInterface $entity_manager, CompagnieRepository $compagnie_repository,Request $request, int $id, PoliceAssuranceRepository $policeAssuranceRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        try {
            $policeAssurance = $policeAssuranceRepository->find($id);
            if (!$policeAssurance) {
                return $this->json([
                    'status' => 404,
                    'message' => 'Police d\'assurance not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $policeAssurance->setProprietaireAssurance($data['proprietaireAssurance']);
            $policeAssurance->setBeneficaireAssurance($data['beneficiaireAssurance']);
            $compagnie = $compagnie_repository->find($data['compagnieId']);
            if (!$compagnie) {
                return $this->json([
                    'status' => 404,
                    'message' => 'Compagnie non  trouvee'
                ], Response::HTTP_NOT_FOUND);
            }
            $policeAssurance->setCompagnie($compagnie);
            $entity_manager->flush();

            return $this->json(
                [
                    'status' => 200,
                    'message' => 'Police d\'assurance modifiee avec succes'
                ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la modification de la police d\'assurance'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[Route('api/policeAssurance/delete/{id}', name: 'app_police_assurance_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entity_manager, PoliceAssuranceRepository $policeAssuranceRepository, int $id): Response
    {
        try {
            $policeAssurance = $policeAssuranceRepository->find($id);
            if (!$policeAssurance) {
                return $this->json([
                    'status' => 404,
                    'message' => 'Police d\'assurance not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $entity_manager->remove($policeAssurance);
            $entity_manager->flush();

            return $this->json(
                [
                    'status' => 200,
                    'message' => 'Police d\'assurance supprimee avec succes'
                ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la suppression de la police d\'assurance'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
