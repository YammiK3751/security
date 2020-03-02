<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecurityApartmentRepository")
 */
class SecurityApartment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Document")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    private $document;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Apartment")
     * @ORM\JoinColumn(name="apartment_id", referencedColumnName="id")
     */
    private $apartment;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $compensation;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return float
     */
    public function getCompensation()
    {
        return $this->compensation;
    }

    /**
     * @param float $compensation
     * @return $this
     */
    public function setCompensation($compensation)
    {
        $this->compensation = $compensation;

        return $this;
    }

    /**
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }
}
