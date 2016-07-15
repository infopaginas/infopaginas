<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/13/16
 * Time: 7:57 PM
 */

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\SubscriptionReportManager;
use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Domain\ReportBundle\Model\ExporterInterface;
use Spraed\PDFGeneratorBundle\PDFGenerator\PDFGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class SubscriptionPdfExporter
 * @package Domain\ReportBundle\Export
 */
class SubscriptionPdfExporter extends PdfExporterModel
{
    /**
     * @var SubscriptionReportManager $subscriptionReportManager
     */
    protected $subscriptionReportManager;

    /**
     * @param SubscriptionReportManager $service
     */
    public function setSubscriptionReportManager(SubscriptionReportManager $service)
    {
        $this->subscriptionReportManager = $service;
    }

    /**
     * @param string $code
     * @param string $format
     * @param array $objects
     * @return Response
     */
    public function getResponse(string $code, string $format, array $objects) : Response
    {
        $filename = sprintf(
            '%s_%s.%s',
            'subscription_report',
            date('Y_m_d_H_i_s', strtotime('now')),
            $format
        );

        $subscriptionData = $this->subscriptionReportManager
            ->getSubscriptionsQuantities($objects);

        $html = $this->templateEngine->render(
            'DomainReportBundle:Admin/SubscriptionReport:report.html.twig',
            array(
                'results' => $objects,
                'subscriptionData' => $subscriptionData
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
