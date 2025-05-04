<?php

namespace App\Message;

use App\Entity\Employe;

class NotificationMessage
{
    private string $expediteur;
    private string $destinataire;
    private Employe $contenu;

    public function __construct(string $expediteur, string $destinataire, Employe $contenu)
    {
        $this->expediteur = $expediteur;
        $this->destinataire = $destinataire;
        $this->contenu = $contenu;
    }

    public function getExpediteur(): string
    {
        return $this->expediteur;
    }

    public function getDestinataire(): string
    {
        return $this->destinataire;
    }

    public function getContenu(): Employe
    {
        return $this->contenu;
    }
}
