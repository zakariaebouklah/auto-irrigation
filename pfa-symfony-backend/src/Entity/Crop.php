<?php

namespace App\Entity;

use App\Repository\CropRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CropRepository::class)]
#[UniqueEntity(
    fields: ['cropName'],
    message: 'Crop Already Existing Try New Crop Name Or Select from the available.'
)]
class Crop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups("crop")
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("crop")
     */
    private ?string $cropName = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("crop")
     */
    private ?string $fad = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("crop")
     */
    private ?string $maxRootDepth = null;

    #[ORM\Column]
    /**
     * @Groups("crop")
     */
    private ?bool $harvestedGreen = null;

    #[ORM\ManyToOne(targetEntity: Farmer::class, inversedBy: 'crops')]
    /**
     * @Groups("crop")
     */
    private ?Farmer $owner = null;

    #[ORM\Column]
    /**
     * @Groups("crop")
     */
    private array $stages = [];

    #[ORM\Column(length: 255)]
    /**
     * @Groups("crop")
     */
    private ?string $sowDepth = null;

    #[ORM\OneToMany(mappedBy: 'crop', targetEntity: Output::class, cascade: ["remove"])]
    private Collection $outputs;

    #[ORM\OneToMany(mappedBy: 'crop', targetEntity: Parcel::class)]
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

    public function getCropName(): ?string
    {
        return $this->cropName;
    }

    public function setCropName(string $cropName): self
    {
        $this->cropName = $cropName;

        return $this;
    }

    public function getFad(): ?string
    {
        return $this->fad;
    }

    public function setFad(string $fad): self
    {
        $this->fad = $fad;

        return $this;
    }

    public function getMaxRootDepth(): ?string
    {
        return $this->maxRootDepth;
    }

    public function setMaxRootDepth(string $maxRootDepth): self
    {
        $this->maxRootDepth = $maxRootDepth;

        return $this;
    }

    public function isHarvestedGreen(): ?bool
    {
        return $this->harvestedGreen;
    }

    public function setHarvestedGreen(bool $harvestedGreen): self
    {
        $this->harvestedGreen = $harvestedGreen;

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

    public function getStages(): array
    {
        return $this->stages;
    }

    public function setStages(array $stages): self
    {
        $this->stages = $stages;

        return $this;
    }

    public function getSowDepth(): ?string
    {
        return $this->sowDepth;
    }

    public function setSowDepth(string $sowDepth): self
    {
        $this->sowDepth = $sowDepth;

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
            $output->setCrop($this);
        }

        return $this;
    }

    public function removeOutput(Output $output): self
    {
        if ($this->outputs->removeElement($output)) {
            // set the owning side to null (unless already changed)
            if ($output->getCrop() === $this) {
                $output->setCrop(null);
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
            $parcel->setCrop($this);
        }

        return $this;
    }

    public function removeParcel(Parcel $parcel): self
    {
        if ($this->parcels->removeElement($parcel)) {
            // set the owning side to null (unless already changed)
            if ($parcel->getCrop() === $this) {
                $parcel->setCrop(null);
            }
        }

        return $this;
    }
}
