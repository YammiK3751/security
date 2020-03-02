<?php

namespace App\Repository;

use App\Entity\Apartment;
use App\Entity\Crew;
use App\Entity\Departure;
use App\Entity\Document;
use App\Entity\House;
use App\Entity\MonthlyFee;
use App\Entity\PaymentOrder;
use App\Entity\Region;
use App\Entity\SecurityApartment;
use App\Entity\Street;
use Doctrine\Bundle\DoctrineBundle\Registry;

Trait RepositoryAwareTrait
{
    /**
     * @return Registry
     */
    abstract protected function getDoctrine();

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return StreetRepository
     */
    protected function getStreetRepository()
    {
        return $this->getDoctrine()->getRepository(Street::class);
    }

    /**
     * @return RegionRepository
     */
    protected function getRegionRepository()
    {
        return $this->getDoctrine()->getRepository(Region::class);
    }

    /**
     * @return HouseRepository
     */
    protected function getHouseRepository()
    {
        return $this->getDoctrine()->getRepository(House::class);
    }

    /**
     * @return ApartmentRepository
     */
    protected function getApartmentRepository()
    {
        return $this->getDoctrine()->getRepository(Apartment::class);
    }

    /**
     * @return SecurityApartmentRepository
     */
    protected function getSecurityApartmentRepository()
    {
        return $this->getDoctrine()->getRepository(SecurityApartment::class);
    }

    /**
     * @return DocumentRepository
     */
    protected function getDocumentRepository()
    {
        return $this->getDoctrine()->getRepository(Document::class);
    }

    /**
     * @return DepartureRepository
     */
    protected function getDepartureRepository()
    {
        return $this->getDoctrine()->getRepository(Departure::class);
    }

    /**
     * @return PaymentOrderRepository
     */
    protected function getPaymentOrderRepository()
    {
        return $this->getDoctrine()->getRepository(PaymentOrder::class);
    }

    /**
     * @return MonthlyFeeRepository
     */
    protected function getMonthlyFeeRepository()
    {
        return $this->getDoctrine()->getRepository(MonthlyFee::class);
    }

    /**
     * @return CrewRepository
     */
    protected function getCrewRepository()
    {
        return $this->getDoctrine()->getRepository(Crew::class);
    }
}