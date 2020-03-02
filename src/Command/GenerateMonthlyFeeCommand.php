<?php

namespace App\Command;

use App\Entity\Document;
use App\Entity\MonthlyFee;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMonthlyFeeCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:monthly_pay';

    protected function configure()
    {
        $this
            ->setDescription('Command to generate monthly pay')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        $documentRepo = $em->getRepository(Document::class);
        $documents = $documentRepo->findAll();

        if (empty($documents)) {
            $output->writeln(['=========================================================', '']);
            $output->writeln(['Не найдено ни одного договора!', '']);

            return 0;
        }

        /** @var Document $document */
        foreach ($documents as $document) {
            $monthlyFee = new MonthlyFee();
            $monthlyFee
                ->setDocument($document)
                ->setPaymentDate(new \DateTime())
                ->setPaid(false)
            ;
            $em->persist($monthlyFee);
        }
        $em->flush();

        $output->writeln(['=========================================================', '']);
        $output->writeln(['Выполнено!', '']);

        return 0;
    }
}
