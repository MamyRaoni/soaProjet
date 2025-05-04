<?php

namespace App\Message;

class NotificationMessage
{
    private string $expediteur;
    private string $destinataire;
    private string $contenu;

    public function __construct(string $expediteur, string $destinataire, string $contenu)
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

    public function getContenu(): string
    {
        return $this->contenu;
    }
}
