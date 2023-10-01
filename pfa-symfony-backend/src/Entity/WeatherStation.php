<?php

namespace App\Entity;

use App\Repository\WeatherStationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeatherStationRepository::class)]
class WeatherStation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $stationName = null;

    #[ORM\OneToMany(mappedBy: 'weatherStation', targetEntity: Farm::class)]
    private Collection $farms;

    public function __construct()
    {
        $this->farms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStationName(): ?string
    {
        return $this->stationName;
    }

    public function setStationName(string $stationName): self
    {
        $this->stationName = $stationName;

        return $this;
    }

    /**
     * @return Collection<int, Farm>
     */
    public function getFarms(): Collection
    {
        return $this->farms;
    }

    public function addFarm(Farm $farm): self
    {
        if (!$this->farms->contains($farm)) {
            $this->farms->add($farm);
            $farm->setWeatherStation($this);
        }

        return $this;
    }

    public function removeFarm(Farm $farm): self
    {
        if ($this->farms->removeElement($farm)) {
            // set the owning side to null (unless already changed)
            if ($farm->getWeatherStation() === $this) {
                $farm->setWeatherStation(null);
            }
        }

        return $this;
    }

}
