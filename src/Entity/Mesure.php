<?php

namespace App\Entity;

use App\Repository\MesureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MesureRepository::class)
 */
class Mesure
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $water;

    /**
     * @ORM\Column(type="float")
     */
    private $electricity;

    /**
     * @ORM\Column(type="float")
     */
    private $gas;

    /**
     * @ORM\Column(type="float")
     */
    private $waste;

    /**
     * @ORM\Column(type="boolean")
     */
    private $navigoSubscription;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="mesures")
     */
    private $toMesure;

    /**
     * @ORM\ManyToOne(targetEntity=Date::class, inversedBy="perform")
     * @ORM\JoinColumn(nullable=false)
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWater(): ?float
    {
        return $this->water;
    }

    public function setWater(float $water): self
    {
        $this->water = $water;

        return $this;
    }

    public function getElectricity(): ?float
    {
        return $this->electricity;
    }

    public function setElectricity(float $electricity): self
    {
        $this->electricity = $electricity;

        return $this;
    }

    public function getGas(): ?float
    {
        return $this->gas;
    }

    public function setGas(float $gas): self
    {
        $this->gas = $gas;

        return $this;
    }

    public function getWaste(): ?float
    {
        return $this->waste;
    }

    public function setWaste(float $waste): self
    {
        $this->waste = $waste;

        return $this;
    }

    public function getNavigoSubscription(): ?bool
    {
        return $this->navigoSubscription;
    }

    public function setNavigoSubscription(bool $navigoSubscription): self
    {
        $this->navigoSubscription = $navigoSubscription;

        return $this;
    }

    public function getToMesure(): ?User
    {
        return $this->toMesure;
    }

    public function setToMesure(?User $toMesure): self
    {
        $this->toMesure = $toMesure;

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
