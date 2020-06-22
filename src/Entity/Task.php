<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $unit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validate;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="tasks")
     */
    private $toAchieve;

    /**
     * @ORM\ManyToOne(targetEntity=Date::class, inversedBy="belongs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $date;

    public function __construct()
    {
        $this->toAchieve = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getToAchieve(): Collection
    {
        return $this->toAchieve;
    }

    public function addAchieve(User $achieve): self
    {
        if (!$this->toAchieve->contains($achieve)) {
            $this->toAchieve[] = $achieve;
        }

        return $this;
    }

    public function removeAchieve(User $achieve): self
    {
        if ($this->toAchieve->contains($achieve)) {
            $this->toAchieve->removeElement($achieve);
        }

        return $this;
    }

    public function getDate(): ?Date
    {
        return $this->date;
    }

    public function setDate(?Date $date): self
    {
        $this->date = $date;

        return $this;
    }
}
