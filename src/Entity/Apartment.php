<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApartmentRepository")
 */
class Apartment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\House")
     * @ORM\JoinColumn(name="house_id", referencedColumnName="id")
     */
    private $house;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return House
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * @param House $house
     * @return $this
     */
    public function setHouse($house)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        /** @var House $house */
        $house = $this->getHouse();

        $address = $house->getRegion()->getName() . ', ' . $house->getStreet()->getName() . ', д. ' . $house->getNumber() . ', кв. ' . $this->getNumber();

        return $address;
    }
}
