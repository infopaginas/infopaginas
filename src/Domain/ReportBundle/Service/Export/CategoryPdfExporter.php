<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\CategoryOverviewReportManager;use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryPdfExporter
 * @package Domain\ReportBundle\Export
 */
class CategoryPdfExporter extends PdfExporterModel
{
    /**
     * @var CategoryOverviewReportManager $categoryOverviewReportManager
     */
    protected $categoryOverviewReportManager;

    /**
     * @param CategoryOverviewReportManager $service
     */
    public function setCategoryOverviewReportManager(CategoryOverviewReportManager $service)
    {
        $this->categoryOverviewReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     */
    public function getResponse($params = [])
    {
        $filename = $this->categoryOverviewReportManager->generateReportName(self::FORMAT);

        $categoryData = $this->categoryOverviewReportManager->getCategoryReportData($params, false);

        $html = $this->templateEngine->render(
            'DomainReportBundle:Admin/CategoryReport:pdf_report.html.twig',
            [
                'categoryData' => $categoryData,
                'areas' => $this->categoryOverviewReportManager->getAreas(),
            ]
        );

        return $this->sendResponse($html, $filename);
    }
}
