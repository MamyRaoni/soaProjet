<?php

namespace App\Controller;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class NotificationController extends AbstractController
{
    #[Route('/api/notification/create', name: 'app_notification_create', methods: ['POST'])]
    public function create(EntityManagerInterface $entity_manager,Request $request):Response{
        $data = json_decode($request->getContent(),true);
        try{
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
        catch(\Exception $e){
            return $this->json([
                'status'=>500,
                'message'=>'Erreur lors de l\'insertion de la notification'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route("api/notification/get/{id}",name:"app_notification_get",methods:["GET"])]
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
    #[Route("api/notification/getAll",name:"app_notification_getAll",methods:["GET"])]
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
    #[Route("api/notification/delete/{id}",name:"app_notification_delete",methods:["DELETE"])]
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
    #[Route("api/notification/update/{id}",name:"app_notification_update",methods:["PATCH"])]
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
