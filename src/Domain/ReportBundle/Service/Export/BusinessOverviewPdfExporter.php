<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/13/16
 * Time: 7:57 PM
 */

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BusinessOverviewPdfExporter
 * @package Domain\ReportBundle\Export
 */
class BusinessOverviewPdfExporter extends PdfExporterModel
{
    /**
     * @var BusinessOverviewReportManager $businessOverviewReportManager
     */
    protected $businessOverviewReportManager;

    /**
     * @param BusinessOverviewReportManager $service
     */
    public function setBusinessOverviewReportManager(BusinessOverviewReportManager $service)
    {
        $this->businessOverviewReportManager = $service;
    }

    /**
     * @param string $code
     * @param string $format
     * @param array $filterParams
     * @return Response
     */
    public function getResponse(string $code, string $format, array $filterParams) : Response
    {
        list($businessOverviewData, $filename) =
            $this->businessOverviewReportManager->getBusinessOverviewReportDataAndName($filterParams, $format);

        $html = $this->templateEngine->render(
            'DomainReportBundle:Admin/BusinessOverviewReport:pdf_report.html.twig',
            array(
                'data' => $businessOverviewData
            )
        );

        $content = $this->pdfGenerator->generatePDF($html, 'UTF-8');

        return new Response(
            $content,
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename=%s', $filename)
            )
        );
    }
}
