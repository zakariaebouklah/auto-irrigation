<?php

namespace App\Entity;

use App\Repository\SoilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SoilRepository::class)]
#[UniqueEntity(
    fields: ['type'],
    message: 'Soil type Already Existing Try New Soil Name Or Select from the available.'
)]
class Soil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups("soil")
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("soil")
     */
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("soil")
     */
    private ?string $paw = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("soil")
     */
    private ?string $depth = null;

    #[ORM\ManyToOne(inversedBy: 'soils')]
    /**
     * @Groups("soil")
     */
    private ?Farmer $owner = null;

    #[ORM\OneToMany(mappedBy: 'soil', targetEntity: Output::class, cascade: ["remove"])]
    private Collection $outputs;

    #[ORM\OneToMany(mappedBy: 'soil', targetEntity: Parcel::class)]
    private Collection $parcels;

    public function __construct()
    {
        $this->outputs = new ArrayCollection();
        $this->parcels = new ArrayCollection();
    }

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

    public function getPaw(): ?string
    {
        return $this->paw;
    }

    public function setPaw(string $paw): self
    {
        $this->paw = $paw;

        return $this;
    }

    public function getDepth(): ?string
    {
        return $this->depth;
    }

    public function setDepth(string $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    public function getOwner(): ?Farmer
    {
        return $this->owner;
    }

    public function setOwner(?Farmer $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Output>
     */
    public function getOutputs(): Collection
    {
        return $this->outputs;
    }

    public function addOutput(Output $output): self
    {
        if (!$this->outputs->contains($output)) {
            $this->outputs->add($output);
            $output->setSoil($this);
        }

        return $this;
    }

    public function removeOutput(Output $output): self
    {
        if ($this->outputs->removeElement($output)) {
            // set the owning side to null (unless already changed)
            if ($output->getSoil() === $this) {
                $output->setSoil(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parcel>
     */
    public function getParcels(): Collection
    {
        return $this->parcels;
    }

    public function addParcel(Parcel $parcel): self
    {
        if (!$this->parcels->contains($parcel)) {
            $this->parcels->add($parcel);
            $parcel->setSoil($this);
        }

        return $this;
    }

    public function removeParcel(Parcel $parcel): self
    {
        if ($this->parcels->removeElement($parcel)) {
            // set the owning side to null (unless already changed)
            if ($parcel->getSoil() === $this) {
                $parcel->setSoil(null);
            }
        }

        return $this;
    }

}
