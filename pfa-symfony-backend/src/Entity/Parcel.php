<?php

namespace App\Entity;

use App\Repository\ParcelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ParcelRepository::class)]
class Parcel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups("parcel")
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("parcel")
     */
    private ?string $parcelName = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("parcel")
     */
    private ?string $area = null;

    #[ORM\OneToMany(mappedBy: 'parcel', targetEntity: Probe::class)]
    private Collection $probes;

    #[ORM\ManyToOne(inversedBy: 'parcels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Farm $farm = null;

    #[ORM\ManyToOne(inversedBy: 'parcels')]
    /**
     * @Groups("parcel")
     */
    private ?Soil $soil = null;

    #[ORM\ManyToOne(inversedBy: 'parcels')]
    /**
     * @Groups("parcel")
     */
    private ?Crop $crop = null;

    public function __construct()
    {
        $this->probes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParcelName(): ?string
    {
        return $this->parcelName;
    }

    public function setParcelName(string $parcelName): self
    {
        $this->parcelName = $parcelName;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(string $area): self
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return Collection<int, Probe>
     */
    public function getProbes(): Collection
    {
        return $this->probes;
    }

    public function addProbe(Probe $probe): self
    {
        if (!$this->probes->contains($probe)) {
            $this->probes->add($probe);
            $probe->setParcel($this);
        }

        return $this;
    }

    public function removeProbe(Probe $probe): self
    {
        if ($this->probes->removeElement($probe)) {
            // set the owning side to null (unless already changed)
            if ($probe->getParcel() === $this) {
                $probe->setParcel(null);
            }
        }

        return $this;
    }

    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    public function setFarm(?Farm $farm): self
    {
        $this->farm = $farm;

        return $this;
    }

    public function getSoil(): ?Soil
    {
        return $this->soil;
    }

    public function setSoil(?Soil $soil): self
    {
        $this->soil = $soil;

        return $this;
    }

    public function getCrop(): ?Crop
    {
        return $this->crop;
    }

    public function setCrop(?Crop $crop): self
    {
        $this->crop = $crop;

        return $this;
    }

}
