<?php 
namespace App\MessageHandler;

use App\Message\NotificationMessage;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class NotificationMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(NotificationMessage $message, Mailer $mailer): void
    {
        $notification = new Notification();
        $notification->setExpediteur($message->getExpediteur());
        $notification->setDestinataire($message->getDestinataire());
        $notification->setContenu("modification ou creation sur la police d'assurance de ".$message->getContenu()->getNom());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        // Envoyer un email
        $email = (new Email())
            ->from($message->getExpediteur())
            ->to($message->getDestinataire())
            ->subject('Nouvelle notification')
            ->text("modification ou creation sur la police d'assurance de ".$message->getContenu()->getNom());

        $mailer->send($email);

        echo "Notification sauvegardée en base pour {$message->getDestinataire()}\n";
    }
}
