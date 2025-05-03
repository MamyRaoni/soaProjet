<?php

namespace App\Controller;

use App\Entity\ConseillerRh;
use App\Repository\ConseillerRhRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;



final class ConseillerRhController extends AbstractController
{
    #[OA\Get(
        path: '/api/conseillerRh',
        summary: 'Récupère tous les employés',
        tags: ['conseillerRh'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des conseillerRh récupérée avec succès',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref:new Model(type: \App\Entity\ConseillerRh::class))
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Erreur serveur',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Failed to retrieve data')
                    ]
                )
            )
        ]
    )]
    #[Route('/api/conseillerRh', name: 'app_conseillerGet', methods: ['GET'])]
    public function getAll(ConseillerRhRepository $conseillerRhRepository): JsonResponse
    {
        try {
            $employes = $conseillerRhRepository->findAll();
            return $this->json($employes, 200, []);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Post(
        path: '/api/conseillerRh',
        summary: 'Crée un nouveau conseillerRh',
        tags: ['conseillerRh'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nom', 'email'],
                properties: [
                    new OA\Property(property: 'nom', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john.doe@example.com')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'conseillerRh créé avec succès',
                content: new OA\JsonContent(ref: new Model(type: \App\Entity\ConseillerRh::class))
            ),
            new OA\Response(
                response: 400,
                description: 'Données invalides'
            ),
            new OA\Response(
                response: 500,
                description: 'Erreur serveur'
            )
        ]
    )]
    #[Route('/api/conseillerRh', name:'app_conseillerPost', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse{
        try {
            $data = json_decode($request->getContent(), true);
            $ConseillerRh = new ConseillerRh();
            $ConseillerRh->setNom($data['nom']);
            $ConseillerRh->setEmail($data['email']);
            $entityManager->persist($ConseillerRh);
            $entityManager->flush();
            return $this->json([
                'status' => 'success',
                'message' => 'Data inserted successfully'
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to insert data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Get(
        path: '/api/conseillerRh{id}',
        summary: 'Retrieves a specific conseillerRh by id',
        tags: ['conseillerRh'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path', 
                description: 'ID of conseillerRh to retrieve',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the conseillerRh',
                content: new OA\JsonContent(ref: new Model(type: \App\Entity\ConseillerRh::class))
            ),
            new OA\Response(
                response: 404,
                description: 'conseillerRh not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'conseillerRh not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Failed to retrieve data')
                    ]
                )
            )
        ]
    )]
    #[Route('/api/conseillerRh{id}', name:'app_conseiller_get_id', methods: ['GET'])]
    public function getById(ConseillerRhRepository $conseillerRhRepository, int $id): JsonResponse
    {
        try {
            $ConseillerRh = $conseillerRhRepository->find($id);
            if (!$ConseillerRh) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'conseillerRh not found'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            return $this->json($ConseillerRh, 200, []);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Patch(
        path: '/api/conseillerRh{id}',
        summary: 'Update an conseillerRh by ID',
        tags: ['conseillerRh'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of conseillerRh to update', 
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nom', 'salaire', 'email', 'telephone', 'poste', 'adresse'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@test.com'),
                    new OA\Property(property: 'nom', type: 'string', example: 'John Doe')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'conseillerRh updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Data updated successfully')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'conseillerRh not found'),
            new OA\Response(response: 400, description: 'Invalid data'),
            new OA\Response(response: 500, description: 'Server error')
        ]
    )]
    #[Route('/api/conseillerRh{id}', name:'app_conseiller_update', methods: ['PATCH'])]
    public function updateById(ConseillerRhRepository $conseillerRhRepository, int $id, Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid data'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        try {
            $ConseillerRh = $conseillerRhRepository->find($id);
            if (!$ConseillerRh) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'conseillerRh not found'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            $ConseillerRh->setNom($data['nom']);
            $ConseillerRh->setEmail($data['email']);
            $entityManager->persist(object: $ConseillerRh);
            $entityManager->flush();
            return $this->json([
                'status' => 'success',
                'message' => 'Data updated successfully'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to update data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Delete(
        path: '/api/conseillerRh{id}',
        summary: 'Delete an conseillerRh by ID',
        tags: ['conseillerRh'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of conseillerRh to delete',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'conseillerRh deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Data deleted successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'conseillerRh not found'
            ),
            new OA\Response(
                response: 500,
                description: 'Server error'
            )
        ]
    )]
    #[Route('/api/conseillerRh{id}', name:'app_conseiller_delete', methods: ['DELETE'])]
    public function deleteById(ConseillerRhRepository $conseillerRhRepository, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $ConseillerRh = $conseillerRhRepository->find($id);
            if (!$ConseillerRh) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'conseillerRh not found'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            $entityManager->remove($ConseillerRh);
            $entityManager->flush();
            return $this->json([
                'status' => 'success',
                'message' => 'Data deleted successfully'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to delete data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}
