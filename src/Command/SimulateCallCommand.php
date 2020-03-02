<?php

namespace App\Command;

use App\Entity\Apartment;
use App\Entity\Crew;
use App\Entity\Departure;
use App\Entity\Document;
use App\Entity\PaymentOrder;
use App\Entity\SecurityApartment;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;

class SimulateCallCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $em;

    protected function configure()
    {
        $this
            ->setDescription('Command to simulate call')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Fake call(true/false)')
            ->addArgument('arg2', InputArgument::OPTIONAL, 'Successful departure(true/false)')
        ;
    }

    public function __construct($doctrine)
    {
        parent::__construct();
        $this->em = $doctrine->getManager();
    }

    /**
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->getEm();

        $crewRepo = $em->getRepository(Crew::class);
        $crews = $crewRepo->findAll();
        /** @var Crew $randomCrew */
        $randomCrew = $crews[rand(0, (count($crews) - 1))];

        $helper = $this->getHelper('question');

        $output->writeln(['=========================================================', '']);

        $question = new Question('Fake call true/false (false): ', 'false');
        $arg1 = $helper->ask($input, $output, $question);

        $question = new Question('Successful departure true/false (true) ', 'true');
        $arg2 = $helper->ask($input, $output, $question);

        if (empty($crews)) {
            $output->writeln(['=========================================================', '']);
            $output->writeln(['Не найдено ни одного экипажа!', '']);

            return 0;
        }

        $apartmentRepo = $em->getRepository(Apartment::class);
        $apartments = $apartmentRepo->findAll();
        /** @var Apartment $apartment */
        $randomApartment = $apartments[rand(0, (count($apartments) - 1))];

        if (empty($apartments)) {
            $output->writeln(['=========================================================', '']);
            $output->writeln(['Не найдено ни одной квартиры!', '']);

            return 0;
        }

        $countDepartures = count($em->getRepository(Departure::class)->findAll());

        $departure = new Departure();
        $departure
            ->setCode('В-' . ($countDepartures + 1))
            ->setApartment($randomApartment)
            ->setFakeCall($arg1)
            ->setSuccessfulDeparture($arg2)
            ->setDate(new \DateTime())
            ->setCrew($randomCrew)
        ;
        $em->persist($departure);

        /** @var SecurityApartment $securityApartment */
        $securityApartment = $em->getRepository(SecurityApartment::class)->findOneBy([
           'apartment' => $randomApartment->getId()
        ]);

        /** @var Document $document */
        $document = $securityApartment->getDocument();

        $countPaymentOrders = count($em->getRepository(PaymentOrder::class)->findAll());

        $paymentOrder = new PaymentOrder();
        $paymentOrder
            ->setCode('РЛ-' . ($countPaymentOrders + 1))
            ->setDocument($document)
            ->setDeparture($departure)
        ;

        if ($arg1 && !$arg2) {
            $paymentOrder
                ->setType(1)
                ->setAmount($securityApartment->getCompensation() * 0.05)
            ;

            $output->writeln(['=========================================================', '']);
            $output->writeln(['Был совершён ложный вызов по адресу: ' . $securityApartment->getApartment()->getAddress(), '']);
            $output->writeln(['В следствии этого был выписан штраф на сумму ' . $securityApartment->getCompensation() * 0.05 . ' руб.', '']);

        } elseif (!$arg1 && !$arg2) {
            $paymentOrder
                ->setType(2)
                ->setAmount($securityApartment->getCompensation())
            ;
            $output->writeln(['=========================================================', '']);
            $output->writeln(['Был совершён вызов по адресу: ' . $securityApartment->getApartment()->getAddress(), '']);
            $output->writeln(['Операция по захвату злоумышленика провалилась, клиенту будет предоставлена компенсация в размере ' . $securityApartment->getCompensation() . ' руб.', '']);
        } elseif (!$arg1 && $arg2) {
            $output->writeln(['=========================================================', '']);
            $output->writeln(['Операция по захвату злоумышленика прошла успешно!', '']);

            return 0;
        } else {
            $output->writeln(['=========================================================', '']);
            $output->writeln(['Данная ситуация невозможна!', '']);

            return 0;
        }

        $em->persist($paymentOrder);
        $em->flush();

        return 0;
    }

    /**
     * @param $min
     * @param $max
     * @return bool
     */
    protected function UniqueRandomNumbersWithinRange($min, $max) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return $numbers;
    }
}
