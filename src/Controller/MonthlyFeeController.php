<?php


namespace App\Controller;

use App\Entity\MonthlyFee;
use App\Entity\User;
use App\Repository\RepositoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MonthlyFeeController extends AbstractController
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/monthly-fees", name="monthly_fees_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');
        /** @var User $user */
        $user = $this->getUser();

        $payments = $this->getMonthlyFeeRepository()->getAvailablePayments($filters, $user, $orderBy, $order, $currentPage, self::PER_PAGE);

        $maxRows = $payments->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('monthly_fee/list.html.twig', [
            'payments' => $payments,
            'currentPage' => $currentPage,
            'orderBy' => $orderBy,
            'order' => $order,
            'filters' => $filters,
            'perPage' => self::PER_PAGE,
            'maxRows' => $maxRows,
            'maxPages' => $maxPages
        ]);
    }

    /**
     * @Route("/monthly-fees/{id}/pay", name="pay_monthly_fee")
     */
    public function payAction(Request $request)
    {
        $payId = $request->get('id');

        /** @var MonthlyFee $monthlyFee */
        $monthlyFee = $this->getMonthlyFeeRepository()->find($payId);

        $monthlyFee->setPaid(true);

        $em = $this->getEm();
        $em->persist($monthlyFee);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}