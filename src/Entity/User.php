<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="IDX_8D93D6495FB14BA7", columns={"level_id"})})
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("user:read")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     * @Groups("user:read")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
     * @Groups("user:read")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=false)
     * @Groups("user:read")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @Groups("user:read")
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="agora_number", type="integer", nullable=false)
     * @Groups("user:read")
     */
    private $agoraNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_resident", type="integer", nullable=false)
     * @Groups("user:read")
     */
    private $nbResident;

    /**
     * @var float
     *
     * @ORM\Column(name="living_area", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     */
    private $livingArea;

    /**
     * @var bool
     *
     * @ORM\Column(name="gas", type="boolean", nullable=false)
     * @Groups("user:read")
     */
    private $gas;

    /**
     * @var bool
     *
     * @ORM\Column(name="insulation", type="boolean", nullable=false)
     * @Groups("user:read")
     */
    private $insulation;

    /**
     * @var string
     *
     * @ORM\Column(name="social_security_number", type="string", length=30, nullable=false)
     */
    private $socialSecurityNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="gas_average_consumption", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     */
    private $gasAverageConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="water_average_consumption", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     */
    private $waterAverageConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="electricity_average_consumption", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     */
    private $electricityAverageConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="waste_average_consumption", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     */
    private $wasteAverageConsumption;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="registration_date", type="date", nullable=false)
     * @Groups("user:read")
     */
    private $registrationDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="navigo_number", type="integer", nullable=true)
     * @Groups("user:read")
     */
    private $navigoNumber;

    /**
     * @var Level
     *
     * @ORM\ManyToOne(targetEntity="Level")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="level_id", referencedColumnName="id")
     * })
     * @Groups("user:read")
     */
    private $level;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Task", mappedBy="user")
     */
    private $task;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->task = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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
     * @return Collection|Task[]
     */
    public function getTask(): Collection
    {
        return $this->task;
    }

    public function addTask(Task $task): self
    {
        if (!$this->task->contains($task)) {
            $this->task[] = $task;
            $task->addUser($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->task->contains($task)) {
            $this->task->removeElement($task);
            $task->removeUser($this);
        }

        return $this;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
