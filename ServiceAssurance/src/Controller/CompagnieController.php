<?php

namespace App\Controller;

use App\Entity\Compagnie;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompagnieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class CompagnieController extends AbstractController
{   
    #[OA\Post(
        path: '/api/compagnie',
        summary: 'Insert a new insurance company', 
        tags: ['Compagnie'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'nomCompagnie', type: 'string', description: 'Name of the insurance company'),
                    new OA\Property(property: 'attribut', type: 'string', description: 'Attribute of the insurance company'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Email of the insurance company')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Company successfully created',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'nomCompagnie', type: 'string', description: 'Name of the insurance company'),
                        new OA\Property(property: 'attribut', type: 'string', description: 'Attribute of the insurance company'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Email of the insurance company')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error while inserting company',
                content: new OA\JsonContent(
                    type: 'object', 
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 500),
                        new OA\Property(property: 'message', type: 'string', example: 'Erreur lors de l\'insertion de la compagnie')
                    ]
                )
            )
        ]
    )]
    #[Route('/api/compagnie', name: 'app_compagnie_insert',methods:['POST'])]

    public function insert(EntityManagerInterface $entity_manager, Request $request): Response
    {
        $data = json_decode($request->getContent(),true);
        try{
            $compagnie = new Compagnie();
            $compagnie->setNomCompagnie($data['nomCompagnie']);
            $compagnie->setAttribut($data['attribut']);
            $compagnie->setEmail($data['email']);

            $entity_manager->persist($compagnie);
            $entity_manager->flush();

            return $this->json(
                [
                    'status'=>200,
                    'message'=>'Compagne ajoutee avec succes'
                ],Response::HTTP_CREATED);      
        }
        catch(\Exception $e){
            return $this->json([
                'status'=>500,
                'message'=> 'Erreur lors de l\'insertion de la compagnie'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Get(
        path: '/api/compagnie',
        summary: 'Get all insurance companies',
        tags: ['Compagnie'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of all companies',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'nomCompagnie', type: 'string', description: 'Name of the insurance company'),
                        new OA\Property(property: 'attribut', type: 'string', description: 'Attribute of the insurance company'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Email of the insurance company')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error while retrieving companies',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 500),
                        new OA\Property(property: 'message', type: 'string', example: 'Erreur lors de la recuperation des compagnies')
                    ]
                )
            )
        ]
    )]
    #[Route('/api/compagnie',name:'app_compagnie_getAll',methods:['GET'])]
    public function getAll(CompagnieRepository $compagnieRepository):Response
    {
        try{
            $compagnie = $compagnieRepository->findAll();
        return $this->json($compagnie,200,[],['groups'=>['police']]);
        }
        catch(\Exception $e){
            return $this->json([
                'status'=>500,
                'message'=>'Erreur lors de la recuperation des compagnies'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    #[OA\Get(
        path: '/api/compagnie/{id}',
        summary: 'Get a specific insurance company',
        tags: ['Compagnie'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Company found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'nomCompagnie', type: 'string', description: 'Name of the insurance company'),
                        new OA\Property(property: 'attribut', type: 'string', description: 'Attribute of the insurance company'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Email of the insurance company')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Company not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 404),
                        new OA\Property(property: 'message', type: 'string', example: 'Compagnie non trouvee')
                    ]
                )
            )
        ]
    )]

    #[Route('api/compagnie/{id}',name:'app_compagnie_get',methods:['GET'])]
    public function getById(CompagnieRepository $compagnieRepository,int $id):Response{
        try{
            $compagnie = $compagnieRepository->find($id);
            if(!$compagnie){
                return $this->json([
                    'status'=>404,
                    'message'=>'Compagnie non trouvee'
                ],Response::HTTP_NOT_FOUND,[],['groups'=>['police']]);
            }
            return $this->json(
                [
                    'status'=>200,
                    'data'=>$compagnie                
                ],Response::HTTP_OK
            );
        }
        catch(\Exception $e){
            return $this->json([
                'status'=>500,
                'message'=>'Erreur lors de la recuperation de la compagnie'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Patch(
        path: '/api/compagnie/{id}',
        summary: 'Update an insurance company',
        tags: ['Compagnie'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'nomCompagnie', type: 'string'),
                    new OA\Property(property: 'attribut', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Company updated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Compagnie modifiee avec succes')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Company not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 404),
                        new OA\Property(property: 'message', type: 'string', example: 'Compagnie non trouvee')
                    ]
                )
            )
        ]
    )]


    #[Route('/api/compagnie/{id}', name:'app_compagnie_update', methods:['PATCH'])]
    public function update(EntityManagerInterface $entity_manager, Request $request, CompagnieRepository $compagnieRepository, int $id): Response
    {
        try{
            $data = json_decode($request->getContent(), true);
            $compagnie = $compagnieRepository->find($id);
            if (!$compagnie) {
                return $this->json([
                    'status' => 404,
                    'message' => 'Compagnie non trouvee'
                ], Response::HTTP_NOT_FOUND);
            }
            if (isset($data['nomCompagnie'])) {
                $compagnie->setNomCompagnie($data['nomCompagnie']);
            }
            if (isset($data['attribut'])) {
                $compagnie->setAttribut($data['attribut']);
            }
            if (isset($data['email'])) {
                $compagnie->setEmail($data['email']);
            }

            $entity_manager->flush();

            return $this->json([
                'status' => 200,
                'message' => 'Compagnie modifiee avec succes'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la modification de la compagnie'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[OA\Delete(
        path: '/api/compagnie/{id}',
        summary: 'Delete an insurance company',
        tags: ['Compagnie'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Company deleted successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Compagnie supprimee avec succes')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Company not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 404),
                        new OA\Property(property: 'message', type: 'string', example: 'Compagnie non trouvee')
                    ]
                )
            )
        ]
    )]
    #[Route('/api/compagnie/{id}', name:'app_compagnie_delete',methods:['DELETE'])]
    public function delete(EntityManagerInterface $entity_manager, CompagnieRepository $compagnieRepository, int $id): Response
    {
        try{
            $compagnie = $compagnieRepository->find($id);
            if (!$compagnie) {
                return $this->json([
                    'status' => 404,
                    'message' => 'Compagnie non trouvee'
                ], Response::HTTP_NOT_FOUND);
            }
            $entity_manager->remove($compagnie);
            $entity_manager->flush();

            return $this->json([
                'status' => 200,
                'message' => 'Compagnie supprimee avec succes'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la suppression de la compagnie'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
