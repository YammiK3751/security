<?php


namespace App\Controller;

use App\Entity\Document;
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
        $perPage = $request->get('perPage', self::PER_PAGE);
        /** @var User $user */
        $user = $this->getUser();

        $payments = $this->getMonthlyFeeRepository()->getAvailablePayments($filters, $user, $orderBy, $order, $currentPage, $perPage);
        $users = $this->getUserRepository()->findBy(['employeeStatus' => User::EMPLOYEE_STATUS_ACTIVE]);

        $maxRows = $payments->count();
        $maxPages = ceil($maxRows / $perPage);

        return $this->render('monthly_fee/list.html.twig', [
            'payments' => $payments,
            'currentPage' => $currentPage,
            'orderBy' => $orderBy,
            'order' => $order,
            'filters' => $filters,
            'perPage' => $perPage,
            'maxRows' => $maxRows,
            'maxPages' => $maxPages,
            'users' => $users
        ]);
    }

    /**
     * @Route("/monthly-fees/{id}/pay", name="pay_monthly_fee")
     */
    public function payAction(Request $request)
    {
        $payId = $request->get('id');
        $em = $this->getEm();

        /** @var MonthlyFee $monthlyFee */
        $monthlyFee = $this->getMonthlyFeeRepository()->find($payId);
        $monthlyFee->setPaid(true);

        $em->persist($monthlyFee);
        $em->flush();

        $document = $monthlyFee->getDocument();
        if (empty($this->getMonthlyFeeRepository()->findBy(['document' => $document->getId(), 'paid' => false]))) {
            $document->setStatus(Document::DOCUMENT_STATUS_CLOSE);
            $em->persist($document);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }
}