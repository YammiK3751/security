<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 */
class Document
{
    const DOCUMENT_STATUS_NEEDS_APPROVE = 1;
    const DOCUMENT_STATUS_APPROVED = 2;
    const DOCUMENT_STATUS_NEEDS_FIXING = 3;
    const DOCUMENT_STATUS_CANCELLED = 4;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $amountMonthlyFee;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\SecurityApartment", mappedBy="document")
     */
    private $securityApartments;

    public function __construct()
    {
        $this->securityApartments = new ArrayCollection();
        $this->status = self::DOCUMENT_STATUS_NEEDS_APPROVE;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @param \DateTime $startAt
     * @return $this
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @param \DateTime $endAt
     * @return $this
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

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
     * @return float
     */
    public function getAmountMonthlyFee()
    {
        return $this->amountMonthlyFee;
    }

    /**
     * @param float $amountMonthlyFee
     * @return $this
     */
    public function setAmountMonthlyFee($amountMonthlyFee)
    {
        $this->amountMonthlyFee = $amountMonthlyFee;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSecurityApartments()
    {
        return $this->securityApartments;
    }

    /**
     * @param $compensation
     * @return Document
     */
    public function plusCompensation($compensation)
    {
        $this->amountMonthlyFee = $this->getAmountMonthlyFee() + ($compensation * 0.01);

        return $this;
    }

    /**
     * @param $compensation
     * @return Document
     */
    public function minusCompensation($compensation)
    {
        $this->amountMonthlyFee = $this->getAmountMonthlyFee() - ($compensation * 0.01);

        return $this;
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::DOCUMENT_STATUS_NEEDS_APPROVE => 'document.needs_approve',
            self::DOCUMENT_STATUS_APPROVED => 'document.approved',
            self::DOCUMENT_STATUS_NEEDS_FIXING => 'document.needs_fixing',
            self::DOCUMENT_STATUS_CANCELLED => 'document.cancelled',
        ];
    }

    /**
     * @return array
     */
    public function getStatusColor()
    {
        return [
            self::DOCUMENT_STATUS_NEEDS_APPROVE => 'inverse',
            self::DOCUMENT_STATUS_APPROVED => 'success',
            self::DOCUMENT_STATUS_NEEDS_FIXING => 'warning',
            self::DOCUMENT_STATUS_CANCELLED => 'danger'
        ];
    }
}
