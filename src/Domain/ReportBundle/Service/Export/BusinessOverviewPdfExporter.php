<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\ViewsAndVisitorsReportManager;
use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BusinessOverviewPdfExporter
 * @package Domain\ReportBundle\Export
 */
class BusinessOverviewPdfExporter extends PdfExporterModel
{
    /**
     * @var ViewsAndVisitorsReportManager $businessOverviewReportManager
     */
    protected $viewsAndVisitorsReportManager;

    /**
     * @param ViewsAndVisitorsReportManager $service
     */
    public function setViewsAndVisitorsReportManager(ViewsAndVisitorsReportManager $service)
    {
        $this->viewsAndVisitorsReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     */
    public function getResponse($params = []) : Response
    {
        $filename = $this->viewsAndVisitorsReportManager->generateReportName(self::FORMAT);

        $businessOverviewData = $this->viewsAndVisitorsReportManager->getViewsAndVisitorsData($params);

        $html = $this->templateEngine->render(
            'DomainReportBundle:Admin/BusinessOverviewReport:pdf_report.html.twig',
            array(
                'viewsAndVisitorsData' => $businessOverviewData
            )
        );

        return $this->sendResponse($html, $filename);
    }
}
