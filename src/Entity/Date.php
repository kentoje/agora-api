<?php

namespace App\Entity;

use App\Repository\DateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DateRepository::class)
 */
class Date
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=Mesure::class, mappedBy="date")
     */
    private $toPerform;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="date")
     */
    private $toBelong;

    public function __construct()
    {
        $this->toPerform = new ArrayCollection();
        $this->toBelong = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Mesure[]
     */
    public function getToPerform(): Collection
    {
        return $this->toPerform;
    }

    public function addPerform(Mesure $perform): self
    {
        if (!$this->toPerform->contains($perform)) {
            $this->toPerform[] = $perform;
            $perform->setDate($this);
        }

        return $this;
    }

    public function removePerform(Mesure $perform): self
    {
        if ($this->toPerform->contains($perform)) {
            $this->toPerform->removeElement($perform);
            // set the owning side to null (unless already changed)
            if ($perform->getDate() === $this) {
                $perform->setDate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getToBelong(): Collection
    {
        return $this->toBelong;
    }

    public function addBelong(Task $belong): self
    {
        if (!$this->toBelong->contains($belong)) {
            $this->toBelong[] = $belong;
            $belong->setDate($this);
        }

        return $this;
    }

    public function removeBelong(Task $belong): self
    {
        if ($this->toBelong->contains($belong)) {
            $this->toBelong->removeElement($belong);
            // set the owning side to null (unless already changed)
            if ($belong->getDate() === $this) {
                $belong->setDate(null);
            }
        }

        return $this;
    }
}
