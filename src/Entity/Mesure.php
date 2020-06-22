<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mesure
 *
 * @ORM\Table(name="mesure", indexes={@ORM\Index(name="IDX_5F1B6E707208F58B", columns={"to_mesure_id"}), @ORM\Index(name="IDX_5F1B6E70B897366B", columns={"date_id"})})
 * @ORM\Entity
 */
class Mesure
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="water", type="float", precision=10, scale=0, nullable=false)
     */
    private $water;

    /**
     * @var float
     *
     * @ORM\Column(name="electricity", type="float", precision=10, scale=0, nullable=false)
     */
    private $electricity;

    /**
     * @var float
     *
     * @ORM\Column(name="gas", type="float", precision=10, scale=0, nullable=false)
     */
    private $gas;

    /**
     * @var float
     *
     * @ORM\Column(name="waste", type="float", precision=10, scale=0, nullable=false)
     */
    private $waste;

    /**
     * @var bool
     *
     * @ORM\Column(name="navigo_subscription", type="boolean", nullable=false)
     */
    private $navigoSubscription;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="to_mesure_id", referencedColumnName="id")
     * })
     */
    private $toMesure;

    /**
     * @var Date
     *
     * @ORM\ManyToOne(targetEntity="Date")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="date_id", referencedColumnName="id")
     * })
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
