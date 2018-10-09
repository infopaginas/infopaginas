<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryPdfExporter
 * @package Domain\ReportBundle\Export
 */
class CategoryPdfExporter extends PdfExporterModel
{
    /**
     * @var CategoryReportManager $categoryReportManager
     */
    protected $categoryReportManager;

    /**
     * @param CategoryReportManager $service
     */
    public function setCategoryReportManager(CategoryReportManager $service)
    {
        $this->categoryReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     */
    public function getResponse($params = [])
    {
        $filename = $this->categoryReportManager->generateReportName(self::FORMAT);

        $categoryData = $this->categoryReportManager->getCategoryReportData($params, false);

        $html = $this->templateEngine->render(
            'DomainReportBundle:Admin/CategoryReport:pdf_report.html.twig',
            [
                'categoryData' => $categoryData,
            ]
        );

        return $this->sendResponse($html, $filename);
    }
}
