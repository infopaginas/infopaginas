<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\Exporter\PdfExporterModel;
use Domain\ReportBundle\Util\DatesUtil;
use Symfony\Component\HttpFoundation\Response;

class BusinessReportPdfExporter extends PdfExporterModel
{
    /**
     * @var BusinessOverviewReportManager $businessOverviewReportManager
     */
    protected $businessOverviewReportManager;

    /**
     * @var KeywordsReportManager $keywordsReportManager
     */
    protected $keywordsReportManager;

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
     * @param KeywordsReportManager $service
     */
    public function setKeywordsReportManager(KeywordsReportManager $service)
    {
        $this->keywordsReportManager = $service;
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
        $currentYearParams  = $this->businessOverviewReportManager->getThisYearSearchParams($params);
        $previousYearParams = $this->businessOverviewReportManager->getThisLastSearchParams($params);

        $interactionCurrentData  = $this->businessOverviewReportManager->getBusinessOverviewReportData($params);

        $keywordsData = $this->keywordsReportManager->getKeywordsData($params);

        $interactionCurrentYearData  = $this->businessOverviewReportManager
            ->getBusinessOverviewReportData($currentYearParams);
        $interactionPreviousYearData = $this->businessOverviewReportManager
            ->getBusinessOverviewReportData($previousYearParams);

        if ($params['businessProfile']->getDcOrderId()) {
            $adUsageData = $this->adUsageReportManager->getAdUsageData($params);
        } else {
            $adUsageData = [];
        }

        $filename = $this->businessOverviewReportManager
            ->getBusinessOverviewReportName($params['businessProfile']->getSlug(), self::FORMAT);

        $paginatedInteractionData = $this->prepareInteractionDataTable($interactionCurrentData);

        $html = $this->templateEngine->render(
            'DomainReportBundle:PDF:template.html.twig',
            [
                'eventList'                   => BusinessOverviewModel::EVENT_TYPES,
                'businessProfile'             => $params['businessProfile'],
                'interactionCurrentData'      => $interactionCurrentData,
                'paginatedInteractionData'    => $paginatedInteractionData,
                'keywordsData'                => $keywordsData,
                'interactionCurrentYearData'  => $interactionCurrentYearData,
                'interactionPreviousYearData' => $interactionPreviousYearData,
                'adUsageData'                 => $adUsageData,
            ]
        );

        return $this->sendResponse($html, $filename, $params['print']);
    }

    /**
     * @param array $interactionData
     *
     * @return array
     */
    protected function prepareInteractionDataTable($interactionData)
    {
        $eventsPerPage = 5;
        $data = [];

        foreach ($interactionData['results'] as $row) {
            $counter = -1;
            $page    = 0;

            foreach ($row as $key => $item) {
                if ($counter >= $eventsPerPage) {
                    $counter = 0;
                    $page++;
                    $data['results'][$page][$row['date']]['date'] = $row['date'];
                }

                $data['results'][$page][$row['date']][$key] = $item;

                $counter ++;
            }
        }

        return $data;
    }

    protected function getAdUsageReportManager() : AdUsageReportManager
    {
        return $this->adUsageReportManager;
    }

    protected function getKeywordsReportManager() : KeywordsReportManager
    {
        return $this->keywordsReportManager;
    }

    protected function getBusinessOverviewReportManager() : BusinessOverviewReportManager
    {
        return $this->businessOverviewReportManager;
    }
}