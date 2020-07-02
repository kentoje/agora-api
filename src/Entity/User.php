<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @OA\Schema()
 * @OA\SecurityScheme(bearerFormat="JWT", type="apiKey", securityScheme="bearer")
 * @OA\RequestBody(
 *     request="UserLogin",
 *     required=true,
 *     @OA\JsonContent(
 *         required={
 *             "username",
 *             "password",
 *         },
 *         @OA\Property(type="string", property="username"),
 *         @OA\Property(type="string", property="password"),
 *     ),
 * ),
 * @OA\RequestBody(
 *     request="UserSignup",
 *     required=true,
 *     @OA\JsonContent(
 *         required={
 *             "firstName",
 *             "lastName",
 *             "email",
 *             "agoraNumber",
 *             "nbResident",
 *             "livingArea",
 *             "gas",
 *             "insulation",
 *         },
 *         @OA\Property(type="string", property="firstName"),
 *         @OA\Property(type="string", property="lastName"),
 *         @OA\Property(type="string", property="image"),
 *         @OA\Property(type="string", property="email"),
 *         @OA\Property(type="string", property="agoraNumber"),
 *         @OA\Property(type="integer", property="nbResident"),
 *         @OA\Property(type="number", format="float", property="livingArea"),
 *         @OA\Property(type="boolean", property="gas"),
 *         @OA\Property(type="boolean", property="insulation"),
 *         @OA\Property(type="string", property="navigoNumber"),
 *     ),
 * )
 * @ORM\Table(name="user", indexes={@ORM\Index(name="IDX_8D93D6495FB14BA7", columns={"level_id"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"email"})
 * @UniqueEntity(fields={"nifNumber"})
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @OA\Property(type="integer")
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $id;

    /**
     * @var array
     *
     * @OA\Property(
     *     type="array",
     *     @OA\Items(
     *         type="string"
     *     ),
     * )
     * @ORM\Column(name="roles", type="json", nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
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
     * @OA\Property(type="string")
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $firstName;

    /**
     * @var string
     *
     * @OA\Property(type="string")
     * @ORM\Column(name="last_name", type="string", length=50, nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $lastName;

    /**
     * @var string
     *
     * @OA\Property(type="string")
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $image;

    /**
     * @var string
     *
     * @OA\Property(type="string")
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @Assert\Email()
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $email;

    /**
     * @var string
     *
     * @OA\Property(type="string")
     * @ORM\Column(name="agora_number", length=8, type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d{8}$/")
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $agoraNumber;

    /**
     * @var int
     *
     * @OA\Property(type="integer")
     * @ORM\Column(name="nb_resident", type="integer", nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $nbResident;

    /**
     * @var float
     *
     * @OA\Property(type="number", format="float")
     * @ORM\Column(name="living_area", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $livingArea;

    /**
     * @var bool
     *
     * @OA\Property(type="boolean")
     * @ORM\Column(name="gas", type="boolean", nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $gas;

    /**
     * @var bool
     *
     * @OA\Property(type="boolean")
     * @ORM\Column(name="insulation", type="boolean", nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $insulation;

    /**
     * @var string
     *
     * @ORM\Column(name="nif_number", type="string", length=30, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Regex("/^[0-3]\d{12}$/")
     */
    private $nifNumber;

    /**
     * @var float
     *
     * @OA\Property(type="number", format="float")
     * @ORM\Column(name="gas_average_consumption", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $gasAverageConsumption;

    /**
     * @var float
     *
     * @OA\Property(type="number", format="float")
     * @ORM\Column(name="water_average_consumption", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $waterAverageConsumption;

    /**
     * @var float
     *
     * @OA\Property(type="number", format="float")
     * @ORM\Column(name="electricity_average_consumption", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $electricityAverageConsumption;

    /**
     * @var float
     *
     * @OA\Property(type="number", format="float")
     * @ORM\Column(name="waste_average_consumption", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $wasteAverageConsumption;

    /**
     * @var DateTime
     *
     * @OA\Property(type="string", format="date")
     * @ORM\Column(name="registration_date", type="date", nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $registrationDate;

    /**
     * @var int|null
     *
     * @OA\Property(type="integer")
     * @ORM\Column(name="navigo_number", type="string", length=8, nullable=true)
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:login")
     */
    private $navigoNumber;

    /**
     * @var Level
     *
     * @OA\Property(ref="#/components/schemas/Level")
     * @ORM\ManyToOne(targetEntity="Level")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="level_id", referencedColumnName="id")
     * })
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:updatable")
     */
    private $level;

    /**
     * @ORM\Column(type="float")
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:updatable")
     */
    private $savingWater;

    /**
     * @ORM\Column(type="float")
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:updatable")
     */
    private $savingTransport;

    /**
     * @ORM\Column(type="float")
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:updatable")
     */
    private $savingElectricity;

    /**
     * @ORM\Column(type="float")
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:updatable")
     */
    private $savingGas;

    /**
     * @ORM\Column(type="float")
     * @Groups("user:read")
     * @Groups("user:create")
     * @Groups("user:updatable")
     */
    private $savingWaste;

    private $additionalDatas;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

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

    public function getAgoraNumber(): ?string
    {
        return $this->agoraNumber;
    }

    public function setAgoraNumber(string $agoraNumber): self
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

    public function getNifNumber(): ?string
    {
        return $this->nifNumber;
    }

    public function setNifNumber(string $nifNumber): self
    {
        $this->nifNumber = $nifNumber;

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

    public function getNavigoNumber(): ?string
    {
        return $this->navigoNumber;
    }

    public function setNavigoNumber(?string $navigoNumber): self
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

    public function getSavingWater(): ?float
    {
        return $this->savingWater;
    }

    public function setSavingWater(float $savingWater): self
    {
        $this->savingWater = $savingWater;

        return $this;
    }

    public function getSavingElectricity(): ?float
    {
        return $this->savingElectricity;
    }

    public function setSavingElectricity(float $savingElectricity): self
    {
        $this->savingElectricity = $savingElectricity;

        return $this;
    }

    public function getSavingGas(): ?float
    {
        return $this->savingGas;
    }

    public function setSavingGas(float $savingGas): self
    {
        $this->savingGas = $savingGas;

        return $this;
    }

    public function getSavingWaste(): ?float
    {
        return $this->savingWaste;
    }

    public function setSavingWaste(float $savingWaste): self
    {
        $this->savingWaste = $savingWaste;

        return $this;
    }

    public function getSavingTransport(): ?float
    {
        return $this->savingTransport;
    }


    public function setSavingTransport(float $savingTransport): self
    {
        $this->savingTransport = $savingTransport;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalDatas()
    {
        return $this->additionalDatas;
    }

    /**
     * @param array $additionalDatas
     */
    public function setAdditionalDatas($additionalDatas): self
    {
        $this->additionalDatas = $additionalDatas;

        return $this;
    }
}
