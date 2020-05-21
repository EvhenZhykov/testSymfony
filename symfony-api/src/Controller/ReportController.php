<?php

namespace App\Controller;

use App\Entity\Report;
use App\Service\ReportHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ReportController
 * @package App\Controller
 */
class ReportController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ReportHelper
     */
    private $reportHelper;

    /**
     * ReportController constructor.
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param RequestStack $request
     * @param ReportHelper $reportHelper
     */
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        RequestStack $request,
        ReportHelper $reportHelper
    )
    {
        $this->em           = $em;
        $this->serializer   = $serializer;
        $this->request      = $request->getCurrentRequest();
        $this->reportHelper = $reportHelper;
    }

    /**
     * @Route("/reports",
     *     name="reports_save",
     *     methods={"POST"})
     * @return Response
     * @throws \Exception
     */
    public function save()
    {
        $file = $this->request->files->all()['reportFile'];
        $content = null;

        if ($file instanceof UploadedFile) {
            $content = file_get_contents($file->getPathname());
        }

        $invoices = $this->serializer->decode($content, 'csv');

        $handledInvoices = $this->reportHelper->handleInvoice($invoices);

        /** @var Report $report */
        $report = new Report();
        $report->setFilename($file->getClientOriginalName());
        $report->setData($handledInvoices);

        $this->em->persist($report);
        $this->em->flush();

        $serializedReport = $this->serializer->serialize($report, 'json');

        return new Response($serializedReport, 201, ['content-type' => 'application/json']);
    }

    /**
     * @Route("/reports/{reportId}",
     *     name="reports_get_by_id",
     *     requirements={"reportId"="\d+"},
     *     methods={"GET"})
     * @param int $reportId
     * @return Response
     */
    public function getById(int $reportId)
    {
        /** @var Report $report */
        $report = $this->em->getRepository(Report::class)->find($reportId);

        if (!$report) {
            return new Response('', 404, ['content-type' => 'application/json']);
        }

        $serializedReport = $this->serializer->serialize($report, 'json');

        return new Response($serializedReport, 200, ['content-type' => 'application/json']);
    }

    /**
 * @Route("/reports",
 *     name="reports_get_all",
 *     methods={"GET"})
 * @return Response
 */
    public function getAll()
    {
        $reports = $this->em->getRepository(Report::class)->findAll();

        $serializedReports = $this->serializer->serialize($reports, 'json');

        return new Response($serializedReports, 200, ['content-type' => 'application/json']);
    }

    /**
     * @Route("/reports/file-names",
     *     name="reports_file_names",
     *     methods={"GET"})
     * @return Response
     */
    public function getAllFileNames()
    {
        $reports = $this->em->getRepository(Report::class)->getAllFileNames();

        $serializedReports = $this->serializer->serialize($reports, 'json');

        return new Response($serializedReports, 200, ['content-type' => 'application/json']);
    }
}