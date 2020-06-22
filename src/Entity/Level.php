<?php

namespace App\Entity;

use App\Repository\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LevelRepository::class)
 */
class Level
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $levelNumber;

    /**
     * @ORM\Column(type="float")
     */
    private $reductionRate;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="level")
     */
    private $toConcern;

    public function __construct()
    {
        $this->toConcern = new ArrayCollection();
    }

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

    /**
     * @return Collection|User[]
     */
    public function getToConcern(): Collection
    {
        return $this->toConcern;
    }

    public function addConcerned(User $concerned): self
    {
        if (!$this->toConcern->contains($concerned)) {
            $this->toConcern[] = $concerned;
            $concerned->setLevel($this);
        }

        return $this;
    }

    public function removeConcerned(User $concerned): self
    {
        if ($this->toConcern->contains($concerned)) {
            $this->toConcern->removeElement($concerned);
            // set the owning side to null (unless already changed)
            if ($concerned->getLevel() === $this) {
                $concerned->setLevel(null);
            }
        }

        return $this;
    }
}
