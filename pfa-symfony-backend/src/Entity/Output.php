<?php

namespace App\Entity;

use App\Repository\OutputRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OutputRepository::class)]
class Output
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups("output")
     */
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?\DateTimeImmutable $dateOfCalculations = null;

    #[ORM\ManyToOne(inversedBy: 'outputs')]
    private ?Farmer $owner = null;

    #[ORM\ManyToOne(inversedBy: 'outputs')]
    private ?Crop $crop = null;

    #[ORM\ManyToOne(inversedBy: 'outputs')]
    private ?Soil $soil = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $tMax = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $tMin = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $rHmax = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $rHmin = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $windSpeed = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $sRad = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $precipitations = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $et0 = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $kc = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $zRoot = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $zRootReal = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $swd = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $swdc = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $irr = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups("output")
     */
    private ?float $etc = null;

    #[ORM\Column(nullable: true, options: ['default' => 1])]
    /**
     * @Groups("output")
     */
    private ?int $das = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTMax(): ?string
    {
        return $this->tMax;
    }

    public function setTMax(?string $tMax): self
    {
        $this->tMax = $tMax;

        return $this;
    }

    public function getTMin(): ?string
    {
        return $this->tMin;
    }

    public function setTMin(?string $tMin): self
    {
        $this->tMin = $tMin;

        return $this;
    }

    public function getRHmin(): ?string
    {
        return $this->rHmin;
    }

    public function setRHmin(?string $rHmin): self
    {
        $this->rHmin = $rHmin;

        return $this;
    }

    public function getRHmax(): ?string
    {
        return $this->rHmax;
    }

    public function setRHmax(?string $rHmax): self
    {
        $this->rHmax = $rHmax;

        return $this;
    }

    public function getWindSpeed(): ?string
    {
        return $this->windSpeed;
    }

    public function setWindSpeed(?string $windSpeed): self
    {
        $this->windSpeed = $windSpeed;

        return $this;
    }

    public function getSRad(): ?string
    {
        return $this->sRad;
    }

    public function setSRad(?string $sRad): self
    {
        $this->sRad = $sRad;

        return $this;
    }

    public function getPrecipitations(): ?string
    {
        return $this->precipitations;
    }

    public function setPrecipitations(?string $precipitations): self
    {
        $this->precipitations = $precipitations;

        return $this;
    }

    public function getEt0(): ?string
    {
        return $this->et0;
    }

    public function setEt0(?string $et0): self
    {
        $this->et0 = $et0;

        return $this;
    }

    public function getKc(): ?string
    {
        return $this->kc;
    }

    public function setKc(?string $kc): self
    {
        $this->kc = $kc;

        return $this;
    }

    public function getZRoot(): ?string
    {
        return $this->zRoot;
    }

    public function setZRoot(?string $zRoot): self
    {
        $this->zRoot = $zRoot;

        return $this;
    }

    public function getZRootReal(): ?string
    {
        return $this->zRootReal;
    }

    public function setZRootReal(?string $zRootReal): self
    {
        $this->zRootReal = $zRootReal;

        return $this;
    }

    public function getSwd(): ?string
    {
        return $this->swd;
    }

    public function setSwd(?string $swd): self
    {
        $this->swd = $swd;

        return $this;
    }

    public function getSwdc(): ?string
    {
        return $this->swdc;
    }

    public function setSwdc(?string $swdc): self
    {
        $this->swdc = $swdc;

        return $this;
    }

    public function getIrr(): ?string
    {
        return $this->irr;
    }

    public function setIrr(?string $irr): self
    {
        $this->irr = $irr;

        return $this;
    }

    public function getDateOfCalculations(): ?\DateTimeImmutable
    {
        return $this->dateOfCalculations;
    }

    public function setDateOfCalculations(?\DateTimeImmutable $dateOfCalculations): self
    {
        $this->dateOfCalculations = $dateOfCalculations;

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

    public function getCrop(): ?Crop
    {
        return $this->crop;
    }

    public function setCrop(?Crop $crop): self
    {
        $this->crop = $crop;

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

    public function getEtc(): ?float
    {
        return $this->etc;
    }

    public function setEtc(?float $etc): self
    {
        $this->etc = $etc;

        return $this;
    }

    public function getDas(): ?int
    {
        return $this->das;
    }

    public function setDas(?int $das): self
    {
        $this->das = $das;

        return $this;
    }

}
