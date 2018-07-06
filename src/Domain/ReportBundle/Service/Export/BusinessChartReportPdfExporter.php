<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Util\SlugUtil;
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

        /** @var BusinessProfile $params ['businessProfile'] */
        if (isset($params['businessProfile'])) {
            $businessName = SlugUtil::convertSlug($params['businessProfile']->getName());
        }

        $filename = $businessName . '_' . date(self::DATE_EXPORT_FORMAT) . '.' . PdfExporterModel::FORMAT;

        $html = $this->templateEngine->render(
            'DomainReportBundle:PDF:charts-template.html.twig',
            [
                'charts'          => $params['charts'],
                'businessProfile' => $params['businessProfile'],
                'dates'           => $params['dates'],
                'keywordsStats'   => $params['keywordsStats'],
            ]
        );

        return new PdfResponse(
            $this->pdfGenerator->getOutputFromHtml($html),
            $filename
        );
    }
}
