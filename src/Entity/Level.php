<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Level
 *
 * @ORM\Table(name="level")
 * @ORM\Entity
 */
class Level
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
     * @var int
     *
     * @ORM\Column(name="level_number", type="integer", nullable=false)
     * @Groups("user:read")
     */
    private $levelNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="reduction_rate", type="float", precision=10, scale=0, nullable=false)
     * @Groups("user:read")
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
