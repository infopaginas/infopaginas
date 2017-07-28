<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\SubscriptionReportManager;
use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Symfony\Component\HttpFoundation\Response;

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
     * @param array $parameters
     * @return Response
     */
    public function getResponse($parameters = [])
    {
        $filename = $this->subscriptionReportManager->generateReportName(self::FORMAT);

        $subscriptionData = $this->subscriptionReportManager->getSubscriptionsReportData($parameters);

        $html = $this->templateEngine->render(
            'DomainReportBundle:Admin/SubscriptionReport:pdf_report.html.twig',
            [
                'subscriptionData' => $subscriptionData
            ]
        );

        return $this->sendResponse($html, $filename);
    }
}
