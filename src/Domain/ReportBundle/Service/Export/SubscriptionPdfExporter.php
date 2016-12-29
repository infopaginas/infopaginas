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
use Domain\ReportBundle\Util\DatesUtil;
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
     * @param array $parameters
     * @return Response
     */
    public function getResponse(string $code, string $format, array $objects, $parameters) : Response
    {
        $filename = $this->subscriptionReportManager->generateReportName($format, 'subscription_report');

        $subscriptionPlans = $this->subscriptionReportManager->getSubscriptionPlans();

        $dates = $dates = DatesUtil::getReportDates($parameters);

        $subscriptionData = $this->subscriptionReportManager
            ->getSubscriptionsQuantities($objects, $dates, $subscriptionPlans);

        $html = $this->templateEngine->render(
            'DomainReportBundle:Admin/SubscriptionReport:pdf_report.html.twig',
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
