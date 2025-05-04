<?php

namespace App\Entity;

use App\Repository\PoliceAssuranceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PoliceAssuranceRepository::class)]
class PoliceAssurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["compagnie"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["compagnie"])]
    private ?string $proprietaireAssurance = null;

    #[ORM\Column(length: 255)]
    #[Groups(["compagnie"])]
    private ?string $beneficaireAssurance = null;

    #[ORM\ManyToOne(inversedBy: 'policeAssurances')]
    #[Groups(["compagnie"])]
    private ?Compagnie $compagnie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProprietaireAssurance(): ?string
    {
        return $this->proprietaireAssurance;
    }

    public function setProprietaireAssurance(string $proprietaireAssurance): static
    {
        $this->proprietaireAssurance = $proprietaireAssurance;

        return $this;
    }

    public function getBeneficaireAssurance(): ?string
    {
        return $this->beneficaireAssurance;
    }

    public function setBeneficaireAssurance(string $beneficaireAssurance): static
    {
        $this->beneficaireAssurance = $beneficaireAssurance;

        return $this;
    }

    public function getCompagnie(): ?Compagnie
    {
        return $this->compagnie;
    }

    public function setCompagnie(?Compagnie $compagnie): static
    {
        $this->compagnie = $compagnie;

        return $this;
    }
}
