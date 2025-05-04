<?php

namespace App\Controller;

use App\Entity\PoliceAssurance;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PoliceAssuranceRepository;
use App\Repository\CompagnieRepository;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class PoliceAssuranceController extends AbstractController
{
    #[OA\Post(
        path: '/api/policeAssurance',
        summary: 'Creates a new insurance policy',
        description: 'Creates a new insurance policy with the provided details',
        tags: ['Police Assurance'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'proprietaireAssurance', type: 'string', description: 'Insurance policy owner'),
                    new OA\Property(property: 'beneficiaireAssurance', type: 'string', description: 'Insurance policy beneficiary'),
                    new OA\Property(property: 'compagnieId', type: 'integer', description: 'ID of the insurance company')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Insurance policy successfully created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Police d\'assurance ajoutee avec succes')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Insurance company not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 404),
                        new OA\Property(property: 'message', type: 'string', example: 'Compagnie not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 500),
                        new OA\Property(property: 'message', type: 'string', example: 'Erreur lors de l\'insertion de la police d\'assurance')
                    ]
                )
            )
        ]
    )]
    
    #[Route('/api/policeAssurance', name: 'app_police_assurance_insert', methods: ['POST'])]

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
    #[OA\Get(
        path: '/api/policeAssurance',
        summary: 'Retrieves all insurance policies',
        description: 'Gets a list of all insurance policies with their details',
        tags: ['Police Assurance'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of insurance policies successfully retrieved',
                content: new OA\JsonContent(
                    type:'array', 
                    items:new OA\Items(new Model(type: PoliceAssurance::class))),
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 500),
                        new OA\Property(property: 'message', type: 'string', example: 'Erreur lors de la récupération des polices d\'assurance')
                    ]
                )
            )
        ]
    )]

    #[Route('/api/policeAssurance', name: 'app_police_assurance_getAll', methods: ['GET'])]
    public function getAll(PoliceAssuranceRepository $policeAssuranceRepository): Response
    {
        try {
            $policeAssurances = $policeAssuranceRepository->findAll();
            return $this->json($policeAssurances,200,[],['groups'=>['compagnie']]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la récupération des polices d\'assurance: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Get(
        path: '/api/policeAssurance/{id}',
        summary: 'Retrieves a specific insurance policy',
        description: 'Gets details of an insurance policy by its ID',
        tags: ['Police Assurance'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Insurance policy ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Insurance policy successfully retrieved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Police d\'assurance récupérée avec succès'),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'proprietaireAssurance', type: 'string'),
                            new OA\Property(property: 'beneficaireAssurance', type: 'string'),
                            new OA\Property(property: 'compagnie', type: 'object', properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'nom', type: 'string')
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Insurance policy not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 404),
                        new OA\Property(property: 'message', type: 'string', example: 'Police d\'assurance non trouvée')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 500),
                        new OA\Property(property: 'message', type: 'string', example: 'Erreur lors de la récupération de la police d\'assurance')
                    ]
                )
            )
        ]
    )]

    #[Route('api/policeAssurance/{id}', name: 'app_police_assurance_get', methods: ['GET'])]
    public function get(PoliceAssuranceRepository $policeAssuranceRepository, int $id): Response
    {
        try {
            $policeAssurance = $policeAssuranceRepository->find($id);
            if(!$policeAssurance){
                return $this->json([
                    'status'=>404,
                    'message'=>'Compagnie non trouvee'
                ],Response::HTTP_NOT_FOUND,[],['groups'=>['compagnie']]);
            }
            return $this->json(
                [
                    'status'=>200,
                    'data'=>$policeAssurance                
                ],Response::HTTP_OK
            );
            
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la récupération de la police d\'assurance: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[OA\Patch(
        path: '/api/policeAssurance/{id}',
        summary: 'Updates an existing insurance policy',
        description: 'Updates an insurance policy with the provided details',
        tags: ['Police Assurance'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Insurance policy ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'proprietaireAssurance', type: 'string', description: 'Insurance policy owner'),
                    new OA\Property(property: 'beneficiaireAssurance', type: 'string', description: 'Insurance policy beneficiary'),
                    new OA\Property(property: 'compagnieId', type: 'integer', description: 'ID of the insurance company')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Insurance policy successfully updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Police d\'assurance modifiee avec succes')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Insurance policy or company not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 404),
                        new OA\Property(property: 'message', type: 'string', example: 'Police d\'assurance not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 500),
                        new OA\Property(property: 'message', type: 'string', example: 'Erreur lors de la modification de la police d\'assurance')
                    ]
                )
            )
        ]
    )]
    #[Route('api/policeAssurance/{id}', name: 'app_police_assurance_update', methods: ['PATCH'])]    
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
            $policeAssurance->setBeneficaireAssurance($data['beneficaireAssurance']);
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
    
    #[OA\Delete(
        path: '/api/policeAssurance/{id}',
        summary: 'Deletes an insurance policy',
        description: 'Deletes an insurance policy by its ID',
        tags: ['Police Assurance'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Insurance policy ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Insurance policy successfully deleted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Police d\'assurance supprimee avec succes')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Insurance policy not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 404),
                        new OA\Property(property: 'message', type: 'string', example: 'Police d\'assurance not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error', 
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 500),
                        new OA\Property(property: 'message', type: 'string', example: 'Erreur lors de la suppression de la police d\'assurance')
                    ]
                )
            )
        ]
    )]
    #[Route('api/policeAssurance/{id}', name: 'app_police_assurance_delete', methods: ['DELETE'])]
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
