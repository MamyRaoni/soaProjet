<?php

namespace App\Entity;

use App\Repository\CompagnieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompagnieRepository::class)]
class Compagnie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomCompagnie = null;

    #[ORM\Column(length: 255)]
    private ?string $attribut = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @var Collection<int, PoliceAssurance>
     */
    #[ORM\OneToMany(targetEntity: PoliceAssurance::class, mappedBy: 'compagnie')]
    private Collection $policeAssurances;

    public function __construct()
    {
        $this->policeAssurances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCompagnie(): ?string
    {
        return $this->nomCompagnie;
    }

    public function setNomCompagnie(string $nomCompagnie): static
    {
        $this->nomCompagnie = $nomCompagnie;

        return $this;
    }

    public function getAttribut(): ?string
    {
        return $this->attribut;
    }

    public function setAttribut(string $attribut): static
    {
        $this->attribut = $attribut;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, PoliceAssurance>
     */
    public function getPoliceAssurances(): Collection
    {
        return $this->policeAssurances;
    }

    public function addPoliceAssurance(PoliceAssurance $policeAssurance): static
    {
        if (!$this->policeAssurances->contains($policeAssurance)) {
            $this->policeAssurances->add($policeAssurance);
            $policeAssurance->setCompagnie($this);
        }

        return $this;
    }

    public function removePoliceAssurance(PoliceAssurance $policeAssurance): static
    {
        if ($this->policeAssurances->removeElement($policeAssurance)) {
            // set the owning side to null (unless already changed)
            if ($policeAssurance->getCompagnie() === $this) {
                $policeAssurance->setCompagnie(null);
            }
        }

        return $this;
    }
}
