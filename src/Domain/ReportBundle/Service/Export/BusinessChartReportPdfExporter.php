<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Response;

class BusinessChartReportPdfExporter extends PdfExporterModel
{
    /**
     * @param array $params
     * @return Response
     */
    public function getResponse($params = [])
    {
        $filename = 'chart.pdf';

        $html = $this->templateEngine->render(
            'DomainReportBundle:PDF:charts-template.html.twig',
            [
                'charts'          => $params['charts'],
                'businessProfile' => $params['businessProfile'],
            ]
        );

        return new PdfResponse(
            $this->pdfGenerator->getOutputFromHtml($html),
            $filename
        );
    }
}
