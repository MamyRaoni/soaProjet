<?php

namespace App\Controller;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

final class NotificationController extends AbstractController
{
    //attribut api doc
    #[OA\Post(
        path: '/api/notification',
        summary: 'Crée un nouvel notification',
        tags: ['notification'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['destinataire', 'expediteur', 'contenu'],
                properties: [
                    new OA\Property(property: 'destinataire', type: 'string',format:'email', example: 'JohnDoe@email.com'),
                    new OA\Property(property: 'expediteur', type: 'string',format:'email', example: "JohnSow@email.com"),
                    new OA\Property(property: 'contenu', type: 'string', example: 'le contenu de la notification'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'notification créé avec succès',
                content: new OA\JsonContent(ref:new Model(type: \App\Entity\Notification::class))
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
    #[Route('/api/notification', name: 'app_notification_create', methods: ['POST'])]
    public function create(EntityManagerInterface $entity_manager,Request $request):Response{
        $data = json_decode($request->getContent(),true);
        // try{
        //     $notification = new Notification();
        //     $notification->setExpediteur($data['expediteur']);
        //     $notification->setDestinataire($data['destinataire']);
        //     $notification->setContenu($data['contenu']);
        //     $entity_manager->persist($notification);
        //     $entity_manager->flush();
        //     return $this->json([
        //         'status'=>200,
        //         'message'=>'Notification envoye avec succes'
        //     ],Response::HTTP_CREATED);
        // }
        // catch(\Exception $e){
        //     return $this->json([
        //         'status'=>500,
        //         'message'=>'Erreur lors de l\'insertion de la notification'
        //     ],Response::HTTP_INTERNAL_SERVER_ERROR);
        // }
        $notification = new Notification();
            $notification->setExpediteur($data['expediteur']);
            $notification->setDestinataire($data['destinataire']);
            $notification->setContenu($data['contenu']);
            $entity_manager->persist($notification);
            $entity_manager->flush();
            return $this->json([
                'status'=>200,
                'message'=>'Notification envoye avec succes'
            ],Response::HTTP_CREATED);
    }

    //attribut api doc
    #[OA\Get(
        path: '/api/notification/{id}',
        summary: 'Retrieves a specific notification by id',
        tags: ['notification'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of notification to retrieve',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the notification',
                content: new OA\JsonContent(ref: new Model(type: \App\Entity\Notification::class))
            ),
            new OA\Response(
                response: 404,
                description: 'notification not found',
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
       
    #[Route("api/notification/{id}",name:"app_notification_get",methods:["GET"])]
    public function getNotification(NotificationRepository $notificationRepository, int $id): Response
    {
        $notification = $notificationRepository->find($id);
        if (!$notification) {
            return $this->json([
                'status' => 404,
                'message' => 'Notification non trouvée'
            ], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'status' => 200,
            'notification' => [
                'id' => $notification->getId(),
                'expediteur' => $notification->getExpediteur(),
                'destinataire' => $notification->getDestinataire(),
                'contenu' => $notification->getContenu()
            ]
        ]);
    }
    //attribut api doc
    #[OA\Get(
        path: '/api/notification',
        summary: 'Récupère tous les notification',
        tags: ['notification'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des notification récupérée avec succès',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: \App\Entity\Notification::class)))
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
    #[Route("api/notification",name:"app_notification_getAll",methods:["GET"])]
    public function getAllNotifications(NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findAll();
        if (!$notifications) {
            return $this->json([
                'status' => 404,
                'message' => 'Aucune notification trouvée'
            ], Response::HTTP_NOT_FOUND);
        }
        $data = [];
        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'expediteur' => $notification->getExpediteur(),
                'destinataire' => $notification->getDestinataire(),
                'contenu' => $notification->getContenu()
            ];
        }
        return $this->json([
            'status' => 200,
            'notifications' => $data
        ]);
    }
    //attribut api doc
    #[OA\Delete(
        path: '/api/notification/{id}',
        summary: 'Delete an notification by ID',
        tags: ['notification'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of notification to delete',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'notification deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Data deleted successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'notification not found'
            ),
            new OA\Response(
                response: 500,
                description: 'Server error'
            )
        ]
    )] 
    #[Route("api/notification/{id}",name:"app_notification_delete",methods:["DELETE"])]
    public function deleteNotification(NotificationRepository $notificationRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $notification = $notificationRepository->find($id);
        if (!$notification) {
            return $this->json([
                'status' => 404,
                'message' => 'Notification non trouvée'
            ], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($notification);
        $entityManager->flush();
        return $this->json([
            'status' => 200,
            'message' => 'Notification supprimée avec succès'
        ]);
    }
    //attribut api doc
    #[OA\Patch(
        path: '/api/notification/{id}',
        summary: 'Update an notification by ID',
        tags: ['notification'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of notification to update',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['destinataire', 'expediteur', 'contenu'],
                properties: [
                    new OA\Property(property: 'destinataire', type: 'string', format: 'email', example: 'test@test.com'),
                    new OA\Property(property: 'expediteur', type: 'string', format: 'email', example: 'test@test.com'),
                    new OA\Property(property: 'contenu', type: 'string', example: 'contenu de la notification'),
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
    #[Route("api/notification/{id}",name:"app_notification_update",methods:["PATCH"])]
    public function updateNotification(NotificationRepository $notificationRepository, EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $notification = $notificationRepository->find($id);
        if (!$notification) {
            return $this->json([
                'status' => 404,
                'message' => 'Notification non trouvée'
            ], Response::HTTP_NOT_FOUND);
        }
        try {
            if (isset($data['expediteur'])) {
                $notification->setExpediteur($data['expediteur']);
            }
            if (isset($data['destinataire'])) {
                $notification->setDestinataire($data['destinataire']);
            }
            if (isset($data['contenu'])) {
                $notification->setContenu($data['contenu']);
            }
            $entityManager->flush();
            return $this->json([
                'status' => 200,
                'message' => 'Notification mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'message' => 'Erreur lors de la mise à jour de la notification'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
