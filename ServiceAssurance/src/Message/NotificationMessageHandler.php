<?php 
namespace App\MessageHandler;

use App\Message\NotificationMessage;
use App\Entity\Notification;
use App\Entity\PoliceAssurance;
use App\Repository\CompagnieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class NotificationMessageHandler
{
    // public function __construct(
    //     private EntityManagerInterface $entityManager
    // ) {}

    // public function __invoke(NotificationMessage $message,EntityManagerInterface $entity_manager, CompagnieRepository $compagnie_repository): void
    // {
    //     $policeAssurance = new PoliceAssurance();
    //         $policeAssurance->setProprietaireAssurance($message->getContenu()->getNom());
    //         $policeAssurance->setBeneficaireAssurance("vide");
    //         $compagnie = $compagnie_repository->find(1);
    //         if (!$compagnie) {
    //             throw new \RuntimeException("La compagnie d'assurance n'existe pas.");
    //         }
    //         $policeAssurance->setCompagnie($compagnie);
    //         $entity_manager->persist($policeAssurance);
    //         $entity_manager->flush();
    //         $this->entityManager->flush();
    // }
}
