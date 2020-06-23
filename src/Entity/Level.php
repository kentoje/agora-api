<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Level
 *
 * @OA\Schema()
 * @ORM\Table(name="level")
 * @ORM\Entity
 */
class Level
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
     */
    private $id;

    /**
     * @var int
     *
     * @OA\Property(type="integer")
     * @ORM\Column(name="level_number", type="integer", nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     */
    private $levelNumber;

    /**
     * @var float
     *
     * @OA\Property(type="number", format="float")
     * @ORM\Column(name="reduction_rate", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
     * @Groups("user:create")
     */
    private $reductionRate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevelNumber(): ?int
    {
        return $this->levelNumber;
    }

    public function setLevelNumber(int $levelNumber): self
    {
        $this->levelNumber = $levelNumber;

        return $this;
    }

    public function getReductionRate(): ?float
    {
        return $this->reductionRate;
    }

    public function setReductionRate(float $reductionRate): self
    {
        $this->reductionRate = $reductionRate;

        return $this;
    }
}
