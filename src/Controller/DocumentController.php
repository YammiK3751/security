<?php

namespace App\Controller;

use App\Entity\Apartment;
use App\Entity\Document;
use App\Entity\House;
use App\Entity\MonthlyFee;
use App\Entity\Region;
use App\Entity\SecurityApartment;
use App\Entity\Street;
use App\Entity\User;
use App\Repository\RepositoryAwareTrait;
use App\Service\Export\DocumentBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/documents", name="documents_list")
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

        $documents = $this->getDocumentRepository()->getAvailableDocuments($filters, $user, $orderBy, $order, $currentPage, $perPage);
        $users = $this->getUserRepository()->findBy(['employeeStatus' => User::EMPLOYEE_STATUS_ACTIVE]);

        $maxRows = $documents->count();
        $maxPages = ceil($maxRows / $perPage);

        return $this->render('document/list.html.twig', [
            'documents' => $documents,
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
     * Finds and displays a Document entity.
     *
     * @Route("/documents/{id}/details", name="document_details")
     */
    public function detailsAction(Request $request)
    {
        $documentId = $request->get('id');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $regions = $this->getRegionRepository()->findAll();
        $streets = $this->getStreetRepository()->findAll();

        return $this->render('document/details.html.twig', [
            'document' => $document,
            'regions' => $regions,
            'streets' => $streets
        ]);
    }

    /**
     * @Route("/documents/add-document", name="add_document")
     */
    public function addAction(Request $request)
    {
        $documentDetails = $request->get('document');
        /** @var User $user */
        $user = $this->getUser();

        $code = 'Д-' . (count($this->getDocumentRepository()->findAll()) + 1) . '-' . date('Ymd');

        $document = new Document();

        $document
            ->setCode($code)
            ->setOwner($user)
            ->setStartAt(new \DateTime($documentDetails['startAt']))
            ->setEndAt(new \DateTime($documentDetails['endAt']))
            ->setAmountMonthlyFee(0)
            ->setCreatedAt(new \DateTime())
        ;

        $em = $this->getEm();
        $em->persist($document);
        $em->flush();

        return $this->redirectToRoute('document_details', ['id' => $document->getId()]);
    }

    /**
     * @Route("/documents/{id}/edit-document", name="edit_document")
     */
    public function editAction(Request $request)
    {
        $documentId = $request->get('id');
        $documentDetails = $request->get('document');

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $document
            ->setStartAt(new \DateTime($documentDetails['startAt']))
            ->setEndAt(new \DateTime($documentDetails['endAt']))
        ;

        $em = $this->getEm();
        $em->persist($document);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/documents/{id}/change-status/{status}", name="change_document_status")
     */
    public function changeStatusAction(Request $request)
    {
        $documentId = $request->get('id');
        $status = $request->get('status');
        $em = $this->getEm();

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $document->setStatus($status);

        if ($status == Document::DOCUMENT_STATUS_APPROVED) {
            $period = new \DatePeriod($document->getStartAt(), new \DateInterval('P1M'), $document->getEndAt());

            /** @var \DateTime $date */
            foreach ($period as $date) {
                $monthlyFee = new MonthlyFee();
                $monthlyFee
                    ->setDocument($document)
                    ->setPaymentDate($date)
                    ->setPaid(false)
                ;
                $em->persist($monthlyFee);
            }
        }

        $em->persist($document);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/documents/{id}/export-document", name="export_document")
     */
    public function exportDocumentAction(Request $request)
    {
        $documentId = $request->get('id');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $exportBuilder = new DocumentBuilder();
        $phpWordObject = $exportBuilder->build($document);

        $filename = $document->getCode() . '.docx';
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWordObject, 'Word2007');

        $tmp = tempnam('', 'document');

        $writer->save($tmp);

        $headers = [
            'Content-Type' => 'application/docx',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ];

        $response = new Response(file_get_contents($tmp), 200, $headers);

        unlink($tmp);

        return $response;
    }

    /**
     * @Route("/documents/{id}/add-document-item", name="add_document_item")
     */
    public function addItemAction(Request $request)
    {
        $documentId = $request->get('id');
        $itemDetails = $request->get('item');
        $em = $this->getEm();

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $apartment = $this->buildApartment($itemDetails);

        if (!$apartment) {
            return $this->redirect($request->headers->get('referer'));
        }

        $item = new SecurityApartment();
        $item
            ->setDocument($document)
            ->setCompensation($itemDetails['compensation'])
            ->setApartment($apartment)
        ;
        $em->persist($item);

        $document->plusCompensation($itemDetails['compensation']);
        $em->persist($document);

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/documents/{id}/edit-document-item/{itemId}", name="edit_document_item")
     */
    public function editItemAction(Request $request)
    {
        $documentId = $request->get('id');
        $itemId = $request->get('itemId');
        $itemDetails = $request->get('item');
        $em = $this->getEm();

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $apartment = $this->buildApartment($itemDetails);

        if (!$apartment) {
            return $this->redirect($request->headers->get('referer'));
        }

        /** @var SecurityApartment $item */
        $item = $this->getSecurityApartmentRepository()->find($itemId);

        $oldCompensation = $item->getCompensation();

        $item
            ->setApartment($apartment)
            ->setCompensation($itemDetails['compensation'])
        ;
        $em->persist($item);

        if ($oldCompensation != $itemDetails['compensation']) {
            $document
                ->minusCompensation($oldCompensation)
                ->plusCompensation($itemDetails['compensation']);
            $em->persist($document);
        }
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/documents/{id}/remove-document-item/{itemId}", name="remove_document_item")
     */
    public function removeItemAction(Request $request)
    {
        $documentId = $request->get('id');
        $itemId = $request->get('itemId');
        $em = $this->getEm();

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        /** @var SecurityApartment $item */
        $item = $this->getSecurityApartmentRepository()->find($itemId);

        $document->minusCompensation($item->getCompensation());

        $em->persist($document);
        $em->remove($item);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param $itemDetails
     * @return Apartment|bool
     */
    protected function buildApartment($itemDetails)
    {
        $em = $this->getEm();

        /** @var Region $region */
        $region = $this->getRegionRepository()->find($itemDetails['region']);

        /** @var Street $street */
        $street = $this->getStreetRepository()->find($itemDetails['street']);

        /** @var House $house */
        $house = $this->getHouseRepository()->findOneBy([
            'number' => $itemDetails['numberHouse'],
            'street' => $street->getId(),
            'region' => $region->getId()
        ]);

        if (empty($house)) {
            $house = new House();
            $house
                ->setNumber($itemDetails['numberHouse'])
                ->setRegion($region)
                ->setStreet($street)
            ;
            $em->persist($house);
        }

        /** @var Apartment $apartment */
        $apartment = $this->getApartmentRepository()->findOneBy([
            'number' => $itemDetails['numberApartment'],
            'house' => $house->getId()
        ]);

        if (!empty($apartment)) {
            $this->addFlash('danger', 'Выбранная вами квартира уже находится под охраной!');
            return false;
        } else {
            $apartment = new Apartment();
            $apartment
                ->setNumber($itemDetails['numberApartment'])
                ->setHouse($house)
            ;
            $em->persist($apartment);
        }

        return $apartment;
    }
}
