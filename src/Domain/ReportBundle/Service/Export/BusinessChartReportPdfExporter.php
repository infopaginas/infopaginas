<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Response;

class BusinessChartReportPdfExporter extends PdfExporterModel
{
    const DATE_EXPORT_FORMAT = 'Y_m_d_H_i_s';

    /**
     * @param array $params
     * @return Response
     */
    public function getResponse($params = [])
    {
        $businessName = '';

        if (isset($params['businessProfile'])) {
            $businessName = str_replace(' ', '_', $params['businessProfile']->name);
        }

        $filename = $businessName . '_' . date(self::DATE_EXPORT_FORMAT);

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
