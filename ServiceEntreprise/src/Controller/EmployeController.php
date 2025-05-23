<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Repository\ConseillerRhRepository;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class EmployeController extends AbstractController
{
    #[OA\Get(
        path: '/api/employe',
        summary: 'Récupère tous les employés',
        tags: ['Employe'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des employés récupérée avec succès',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: \App\Entity\Employe::class)))
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
    #[Route('/api/employe', name: 'app_employeGet', methods: ['GET'])]
    public function getAll(EmployeRepository $employeRepository): JsonResponse
    {
        try {
            $employes = $employeRepository->findAll();
            return $this->json($employes, 200, []);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Post(
        path: '/api/employe',
        summary: 'Crée un nouvel employé',
        tags: ['Employe'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nom', 'salaire', 'email'],
                properties: [
                    new OA\Property(property: 'nom', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'salaire', type: 'number', format: 'float', example: 50000.00),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john.doe@example.com')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Employé créé avec succès',
                content: new OA\JsonContent(ref:new Model(type: \App\Entity\Employe::class))
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
    #[Route('api/employe', name:'app_employePost', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request, MessageBusInterface $message_bus, ConseillerRhRepository $conseillerRhRepository): JsonResponse{
        try {
            $data = json_decode($request->getContent(), true);
            $employe = new Employe();
            $employe->setNom($data['nom']);
            $employe->setSalaire($data['salaire']);
            $employe->setEmail($data['email']);
            $employe->setTelephone($data['telephone']);
            $employe->setPoste($data['poste']);
            $employe->setAdresse($data['adresse']);
            $conseiller=$conseillerRhRepository->find(1);
            $entityManager->persist($employe);
            $envelope = (new Envelope(
                new \App\Message\NotificationMessage($conseiller->getEmail(), "compagnieAssurance", $employe
                )
            ))->with(new AmqpStamp('entreprise.employee.created'));
            $message_bus->dispatch($envelope);
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
        path: '/api/employe/{id}',
        summary: 'Retrieves a specific employee by id',
        tags: ['Employe'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of employee to retrieve',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the employee',
                content: new OA\JsonContent(ref: new Model(type: \App\Entity\Employe::class))
            ),
            new OA\Response(
                response: 404,
                description: 'Employee not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Employe not found')
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
    #[Route('api/employe/{id}', name:'app_employe_get_id', methods: ['GET'])]
    public function getById(EmployeRepository $employeRepository, int $id): JsonResponse
    {
        try {
            $employe = $employeRepository->find($id);
            if (!$employe) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Employe not found'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            return $this->json($employe, 200, [], ['groups' => ['employe:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Patch(
        path: '/api/employe/{id}',
        summary: 'Update an employee by ID',
        tags: ['Employe'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of employee to update',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nom', 'salaire', 'email', 'telephone', 'poste', 'adresse'],
                properties: [
                    new OA\Property(property: 'telephone', type: 'string', example: '1234567890'),
                    new OA\Property(property: 'poste', type: 'string', example: 'Manager'),
                    new OA\Property(property: 'adresse', type: 'string', example: '123 Main St'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@test.com'),
                    new OA\Property(property: 'nom', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'salaire', type: 'number', format: 'float', example: 50000.00)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Employee updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Data updated successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Employee not found'
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data'
            ),
            new OA\Response(
                response: 500,
                description: 'Server error'
            )
        ]
    )]
    #[Route('api/employe/{id}', name:'app_employe_update', methods: ['PATCH'])]
    public function updateById(EmployeRepository $employeRepository, int $id, Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid data'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        try {
            $employe = $employeRepository->find($id);
            if (!$employe) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Employe not found'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            $employe->setNom($data['nom']);
            $employe->setSalaire($data['salaire']);
            $employe->setEmail($data['email']);
            $employe->setTelephone($data['telephone']);
            $employe->setPoste($data['poste']);
            $employe->setAdresse($data['adresse']);
            $entityManager->persist($employe);
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
        path: '/api/employe/{id}',
        summary: 'Delete an employee by ID',
        tags: ['Employe'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of employee to delete',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Employee deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Data deleted successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Employee not found'
            ),
            new OA\Response(
                response: 500,
                description: 'Server error'
            )
        ]
    )]    
    #[Route('/api/employe/{id}', name:'app_employe_delete', methods: ['DELETE'])]
    public function deleteById(EmployeRepository $employeRepository, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $employe = $employeRepository->find($id);
            if (!$employe) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Employe not found'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            $entityManager->remove($employe);
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
