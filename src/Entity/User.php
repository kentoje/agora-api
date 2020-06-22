<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     */
    private $agoraNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbResident;

    /**
     * @ORM\Column(type="float")
     */
    private $livingArea;

    /**
     * @ORM\Column(type="boolean")
     */
    private $gas;

    /**
     * @ORM\Column(type="boolean")
     */
    private $insulation;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $socialSecurityNumber;

    /**
     * @ORM\Column(type="float")
     */
    private $gasAverageConsumption;

    /**
     * @ORM\Column(type="float")
     */
    private $waterAverageConsumption;

    /**
     * @ORM\Column(type="float")
     */
    private $electricityAverageConsumption;

    /**
     * @ORM\Column(type="float")
     */
    private $wasteAverageConsumption;

    /**
     * @ORM\Column(type="date")
     */
    private $registrationDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $navigoNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Level::class, inversedBy="concerned")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity=Mesure::class, mappedBy="hasMesures")
     */
    private $mesures;

    /**
     * @ORM\ManyToMany(targetEntity=Task::class, mappedBy="achieve")
     */
    private $tasks;

    public function __construct()
    {
        $this->mesures = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->id;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAgoraNumber(): ?int
    {
        return $this->agoraNumber;
    }

    public function setAgoraNumber(int $agoraNumber): self
    {
        $this->agoraNumber = $agoraNumber;

        return $this;
    }

    public function getNbResident(): ?int
    {
        return $this->nbResident;
    }

    public function setNbResident(int $nbResident): self
    {
        $this->nbResident = $nbResident;

        return $this;
    }

    public function getLivingArea(): ?float
    {
        return $this->livingArea;
    }

    public function setLivingArea(float $livingArea): self
    {
        $this->livingArea = $livingArea;

        return $this;
    }

    public function getGas(): ?bool
    {
        return $this->gas;
    }

    public function setGas(bool $gas): self
    {
        $this->gas = $gas;

        return $this;
    }

    public function getInsulation(): ?bool
    {
        return $this->insulation;
    }

    public function setInsulation(bool $insulation): self
    {
        $this->insulation = $insulation;

        return $this;
    }

    public function getSocialSecurityNumber(): ?string
    {
        return $this->socialSecurityNumber;
    }

    public function setSocialSecurityNumber(string $socialSecurityNumber): self
    {
        $this->socialSecurityNumber = $socialSecurityNumber;

        return $this;
    }

    public function getGasAverageConsumption(): ?float
    {
        return $this->gasAverageConsumption;
    }

    public function setGasAverageConsumption(float $gasAverageConsumption): self
    {
        $this->gasAverageConsumption = $gasAverageConsumption;

        return $this;
    }

    public function getWaterAverageConsumption(): ?float
    {
        return $this->waterAverageConsumption;
    }

    public function setWaterAverageConsumption(float $waterAverageConsumption): self
    {
        $this->waterAverageConsumption = $waterAverageConsumption;

        return $this;
    }

    public function getElectricityAverageConsumption(): ?float
    {
        return $this->electricityAverageConsumption;
    }

    public function setElectricityAverageConsumption(float $electricityAverageConsumption): self
    {
        $this->electricityAverageConsumption = $electricityAverageConsumption;

        return $this;
    }

    public function getWasteAverageConsumption(): ?float
    {
        return $this->wasteAverageConsumption;
    }

    public function setWasteAverageConsumption(float $wasteAverageConsumption): self
    {
        $this->wasteAverageConsumption = $wasteAverageConsumption;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getNavigoNumber(): ?int
    {
        return $this->navigoNumber;
    }

    public function setNavigoNumber(?int $navigoNumber): self
    {
        $this->navigoNumber = $navigoNumber;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection|Mesure[]
     */
    public function getMesures(): Collection
    {
        return $this->mesures;
    }

    public function addMesure(Mesure $mesure): self
    {
        if (!$this->mesures->contains($mesure)) {
            $this->mesures[] = $mesure;
            $mesure->setToMesure($this);
        }

        return $this;
    }

    public function removeMesure(Mesure $mesure): self
    {
        if ($this->mesures->contains($mesure)) {
            $this->mesures->removeElement($mesure);
            // set the owning side to null (unless already changed)
            if ($mesure->getToMesure() === $this) {
                $mesure->setToMesure(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->addAchieve($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            $task->removeAchieve($this);
        }

        return $this;
    }
}
