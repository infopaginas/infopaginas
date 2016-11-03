<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/13/16
 * Time: 7:57 PM
 */

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Domain\ReportBundle\Model\ExporterInterface;
use Spraed\PDFGeneratorBundle\PDFGenerator\PDFGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

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
     * @param string $code
     * @param string $format
     * @param array $filterParams
     * @return Response
     */
    public function getResponse(string $code, string $format, array $filterParams) : Response
    {
        $filename = $this->categoryReportManager->generateReportName($format, 'category_report');

        $categoryData = $this->categoryReportManager
            ->getCategoryVisitorsQuantitiesByFilterParams($filterParams);

        $html = $this->templateEngine->render(
            'DomainReportBundle:Admin/CategoryReport:pdf_report.html.twig',
            [
                'categoryData' => $categoryData,
            ]
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
