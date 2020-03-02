<?php


namespace App\Controller;

use App\Entity\User;
use App\Repository\RepositoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentOrderController extends AbstractController
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/payment-orders", name="payment_orders_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');
        /** @var User $user */
        $user = $this->getUser();

        $paymentOrders = $this->getPaymentOrderRepository()->getAvailablePaymentOrders($filters, $user, $orderBy, $order, $currentPage, self::PER_PAGE);

        $maxRows = $paymentOrders->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('payment_order/list.html.twig', [
            'paymentOrders' => $paymentOrders,
            'currentPage' => $currentPage,
            'orderBy' => $orderBy,
            'order' => $order,
            'filters' => $filters,
            'perPage' => self::PER_PAGE,
            'maxRows' => $maxRows,
            'maxPages' => $maxPages
        ]);
    }
}