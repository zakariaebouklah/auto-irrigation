<?php

namespace App\Entity;

use App\Repository\ProbeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProbeRepository::class)]
class Probe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'probes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Parcel $parcel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getParcel(): ?Parcel
    {
        return $this->parcel;
    }

    public function setParcel(?Parcel $parcel): self
    {
        $this->parcel = $parcel;

        return $this;
    }
}
