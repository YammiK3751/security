<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DepartureRepository")
 */
class Departure
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Crew")
     * @ORM\JoinColumn(nullable=false)
     */
    private $crew;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $fakeCall = 0;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $successfulDeparture = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Apartment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $apartment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Crew
     */
    public function getCrew()
    {
        return $this->crew;
    }

    /**
     * @param Crew $crew
     * @return $this
     */
    public function setCrew($crew)
    {
        $this->crew = $crew;

        return $this;
    }

    /**
     * @return bool
     */
    public function getFakeCall()
    {
        return $this->fakeCall;
    }

    /**
     * @param bool $fakeCall
     * @return $this
     */
    public function setFakeCall($fakeCall)
    {
        $this->fakeCall = $fakeCall;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSuccessfulDeparture()
    {
        return $this->successfulDeparture;
    }

    /**
     * @param bool $successfulDeparture
     * @return $this
     */
    public function setSuccessfulDeparture($successfulDeparture)
    {
        $this->successfulDeparture = $successfulDeparture;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Apartment
     */
    public function getApartment()
    {
        return $this->apartment;
    }

    /**
     * @param Apartment $apartment
     * @return $this
     */
    public function setApartment($apartment)
    {
        $this->apartment = $apartment;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
}
