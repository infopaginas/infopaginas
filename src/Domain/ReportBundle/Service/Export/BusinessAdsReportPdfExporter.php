<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Symfony\Component\HttpFoundation\Response;

class BusinessAdsReportPdfExporter extends PdfExporterModel
{
    /**
     * @var BusinessOverviewReportManager $businessOverviewReportManager
     */
    protected $businessOverviewReportManager;

    /**
     * @var AdUsageReportManager $adUsageReportManager
     */
    protected $adUsageReportManager;

    /**
     * @param BusinessOverviewReportManager $service
     */
    public function setBusinessOverviewReportManager(BusinessOverviewReportManager $service)
    {
        $this->businessOverviewReportManager = $service;
    }

    /**
     * @param AdUsageReportManager $service
     */
    public function setAdUsageReportManager(AdUsageReportManager $service)
    {
        $this->adUsageReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     */
    public function getResponse($params = [])
    {
        $interactionCurrentData  = $this->businessOverviewReportManager->getBusinessOverviewReportData($params);
        $adUsageData = $this->adUsageReportManager->getAdUsageData($params);

        $filename = $this->businessOverviewReportManager
            ->getBusinessOverviewReportName($params['businessProfile']->getSlug(), self::FORMAT);

        $html = $this->templateEngine->render(
            'DomainReportBundle:PDF:ads-template.html.twig',
            [
                'eventList'              => BusinessOverviewModel::EVENT_TYPES,
                'businessProfile'        => $params['businessProfile'],
                'interactionCurrentData' => $interactionCurrentData,
                'adUsageData'            => $adUsageData,
            ]
        );

        return $this->sendResponse($html, $filename, $params['print']);
    }

    /**
     * @return AdUsageReportManager
     */
    protected function getAdUsageReportManager() : AdUsageReportManager
    {
        return $this->adUsageReportManager;
    }

    /**
     * @return BusinessOverviewReportManager
     */
    protected function getBusinessOverviewReportManager() : BusinessOverviewReportManager
    {
        return $this->businessOverviewReportManager;
    }
}
