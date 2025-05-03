<?php

namespace App\Controller;

use App\Entity\ConseillerRh;
use App\Repository\ConseillerRhRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;



final class ConseillerRhController extends AbstractController
{
    /**
 * @OA\Get(
 *     path="/api/employe",
 *     summary="Récupère tous les employés",
 *     tags={"Employe"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des employés récupérée avec succès",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Employe"))
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur serveur",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Failed to retrieve data")
 *         )
 *     )
 * )
  */
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
    /**
 * @OA\Post(
 *     path="/api/employe",
 *     summary="Crée un nouvel employé",
 *     tags={"Employe"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nom", "salaire", "email"},
 *             @OA\Property(property="nom", type="string", example="John Doe"),
 *             @OA\Property(property="salaire", type="number", format="float", example=50000.00),
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Employé créé avec succès",
 *         @OA\JsonContent(ref="#/components/schemas/Employe")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur serveur"
 *     )
 * )
 */
    #[Route('/api/conseillerRh', name:'app_employePost', methods: ['POST'])]
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
    /**
     * Get an employee by ID
     * 
     * @OA\Get(
     *     path="/api/employe/{id}",
     *     summary="Retrieves a specific employee by id",
     *     tags={"Employe"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of employee to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns the employee",
     *         @OA\JsonContent(ref="#/components/schemas/Employe")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Employe not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve data")
     *         )
     *     )
     * )
     */
    #[Route('/api/conseillerRh{id}', name:'app_employe_get_id', methods: ['GET'])]
    public function getById(ConseillerRhRepository $conseillerRhRepository, int $id): JsonResponse
    {
        try {
            $ConseillerRh = $conseillerRhRepository->find($id);
            if (!$ConseillerRh) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Employe not found'
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            return $this->json($ConseillerRh, 200, [], ['groups' => ['employe:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @OA\Patch(
     *     path="/api/employe/{id}",
     *     summary="Update an employee by ID",
     *     tags={"Employe"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of employee to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom", "salaire", "email", "telephone", "poste", "adresse"},
     *             @OA\Property(property="telephone", type="string", example="1234567890"),
     *             @OA\Property(property="poste", type="string", example="Manager"),
     *             @OA\Property(property="adresse", type="string", example="123 Main St"),
     *             @OA\Property(property="email", type="string", format="email", example="test@test.com"
     *             @OA\Property(property="nom", type="string", example="John Doe"),
     *             @OA\Property(property="salaire", type="number", format="float", example=50000.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    #[Route('/api/conseillerRh{id}', name:'app_employe_update', methods: ['PATCH'])]
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
                    'message' => 'Employe not found'
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
    /**
     * @OA\Delete(
     *     path="/api/employe/{id}",
     *     summary="Delete an employee by ID",
     *     tags={"Employe"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of employee to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    #[Route('/api/conseillerRh{id}', name:'app_employe_delete', methods: ['DELETE'])]
    public function deleteById(ConseillerRhRepository $conseillerRhRepository, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $ConseillerRh = $conseillerRhRepository->find($id);
            if (!$ConseillerRh) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Employe not found'
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
