<?php

namespace App\Entity;

use App\Repository\FarmerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FarmerRepository::class)]
class Farmer implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups("farmer")
     */
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true, nullable: true)]
    /**
     * @Groups("farmer")
     */
    private ?string $email = null;

    /**
     * @var string[] $roles
     */
    #[ORM\Column]
    /**
     * @Groups("farmer")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    /**
     * @Groups("farmer")
     */
    private string $password;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("farmer")
     */
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    /**
     * @Groups("farmer")
     */
    private ?string $lastName = null;

    #[ORM\Column(length: 20, unique: true)]
    /**
     * @Groups("farmer")
     */
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'farmer', targetEntity: Farm::class)]
    private Collection $farms;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Crop::class)]
    private Collection $crops;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Soil::class)]
    private Collection $soils;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Output::class)]
    private Collection $outputs;

    public function __construct()
    {
        $this->farms = new ArrayCollection();
        $this->crops = new ArrayCollection();
        $this->soils = new ArrayCollection();
        $this->outputs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email ?? $this->phone;
    }

    /**
     * @return string[]
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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
            $farm->setFarmer($this);
        }

        return $this;
    }

    public function removeFarm(Farm $farm): self
    {
        if ($this->farms->removeElement($farm)) {
            // set the owning side to null (unless already changed)
            if ($farm->getFarmer() === $this) {
                $farm->setFarmer(null);
            }
        }

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return Collection<int, Crop>
     */
    public function getCrops(): Collection
    {
        return $this->crops;
    }

    public function addCrop(Crop $crop): self
    {
        if (!$this->crops->contains($crop)) {
            $this->crops->add($crop);
            $crop->setOwner($this);
        }

        return $this;
    }

    public function removeCrop(Crop $crop): self
    {
        if ($this->crops->removeElement($crop)) {
            // set the owning side to null (unless already changed)
            if ($crop->getOwner() === $this) {
                $crop->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Soil>
     */
    public function getSoils(): Collection
    {
        return $this->soils;
    }

    public function addSoil(Soil $soil): self
    {
        if (!$this->soils->contains($soil)) {
            $this->soils->add($soil);
            $soil->setOwner($this);
        }

        return $this;
    }

    public function removeSoil(Soil $soil): self
    {
        if ($this->soils->removeElement($soil)) {
            // set the owning side to null (unless already changed)
            if ($soil->getOwner() === $this) {
                $soil->setOwner(null);
            }
        }

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
            $output->setOwner($this);
        }

        return $this;
    }

    public function removeOutput(Output $output): self
    {
        if ($this->outputs->removeElement($output)) {
            // set the owning side to null (unless already changed)
            if ($output->getOwner() === $this) {
                $output->setOwner(null);
            }
        }

        return $this;
    }
}
