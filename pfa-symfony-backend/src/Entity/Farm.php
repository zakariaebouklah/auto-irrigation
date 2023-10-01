<?php

namespace App\Entity;

use App\Repository\FarmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FarmRepository::class)]
class Farm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups("farm")
     */
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    /**
     * @Groups("farm")
     */
    private ?string $farmName = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("farm")
     */
    private ?string $area = null;

    #[ORM\ManyToOne(inversedBy: 'farms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Farmer $farmer = null;

    #[ORM\ManyToOne(inversedBy: 'farms')]
    /**
     * @Groups("farm")
     */
    private ?WeatherStation $weatherStation = null;

    #[ORM\OneToMany(mappedBy: 'farm', targetEntity: Parcel::class, cascade: ['persist', 'remove'])]
    /**
     * @Groups("farm")
     */
    private Collection $parcels;

    public function __construct()
    {
        $this->parcels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFarmName(): ?string
    {
        return $this->farmName;
    }

    public function setFarmName(string $farmName): self
    {
        $this->farmName = $farmName;

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

    public function getFarmer(): ?Farmer
    {
        return $this->farmer;
    }

    public function setFarmer(?Farmer $farmer): self
    {
        $this->farmer = $farmer;

        return $this;
    }

    public function getWeatherStation(): ?WeatherStation
    {
        return $this->weatherStation;
    }

    public function setWeatherStation(?WeatherStation $weatherStation): self
    {
        $this->weatherStation = $weatherStation;

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
            $parcel->setFarm($this);
        }

        return $this;
    }

    public function removeParcel(Parcel $parcel): self
    {
        if ($this->parcels->removeElement($parcel)) {
            // set the owning side to null (unless already changed)
            if ($parcel->getFarm() === $this) {
                $parcel->setFarm(null);
            }
        }

        return $this;
    }
}
