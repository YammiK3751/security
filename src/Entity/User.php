<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User extends BaseUser
{
    const EMPLOYEE_STATUS_ACTIVE = 'active';
    const EMPLOYEE_STATUS_INACTIVE = 'inactive';
    const EMPLOYEE_STATUS_USER = 'user';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $middlename;

    /**
     * @var string
     *
     * @ORM\Column(name="original_image_url", type="text", nullable=true)
     */
    protected $originalImageUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="text", nullable=true)
     */
    protected $imageUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_status", type="text", nullable=true, options={"default" : "active"})
     */
    protected $employeeStatus = 'active';

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="employment_date", nullable=true)
     */
    protected $employmentDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SecurityApartment", mappedBy="user", orphanRemoval=true)
     */
    private $securityApartments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="owner", orphanRemoval=true)
     */
    private $documents;

    public function __construct()
    {
        parent::__construct();
        $this->securityApartments = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

    /**
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * @param string $middlename
     * @return $this
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalImageUrl()
    {
        return $this->originalImageUrl;
    }

    /**
     * @param $originalImageUrl
     * @return $this
     */
    public function setOriginalImageUrl($originalImageUrl)
    {
        $this->originalImageUrl = $originalImageUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param $imageUrl
     * @return $this
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployeeStatus()
    {
        return $this->employeeStatus;
    }

    /**
     * @param $employeeStatus
     * @return $this
     */
    public function setEmployeeStatus($employeeStatus)
    {
        $this->employeeStatus = $employeeStatus;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEmploymentDate()
    {
        return $this->employmentDate;
    }

    /**
     * @param \DateTime $employmentDate
     * @return $this
     */
    public function setEmploymentDate(\DateTime $employmentDate)
    {
        $this->employmentDate = $employmentDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullName()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    /**
     * @return Collection|SecurityApartment[]
     */
    public function getSecurityApartments(): Collection
    {
        return $this->securityApartments;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setOwner($this);
        }

        return $this;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getOwner() === $this) {
                $document->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public static function getEmployeeStates()
    {
        return [
            self::EMPLOYEE_STATUS_USER => 'Клиент',
            self::EMPLOYEE_STATUS_ACTIVE => 'Active Employee',
            self::EMPLOYEE_STATUS_INACTIVE => 'Inactive Employee',
        ];
    }

    /**
     * @return array
     */
    public static function getEmployeeStatusChoices()
    {
        return array_flip(self::getEmployeeStates());
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_USER')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isEmployee()
    {
        foreach ($this->getGroups() as $group) {
            if ($group->hasRole('ROLE_EMPLOYEE')) {
                return true;
            }
        }

        return false;
    }
}