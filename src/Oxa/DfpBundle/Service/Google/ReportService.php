<?php

namespace Oxa\DfpBundle\Service\Google;

use Oxa\DfpBundle\Google\Api\Ads\Dfp\Lib\DfpUser;
use Oxa\DfpBundle\Model\DataType\DateRangeInterface;
use Oxa\DfpBundle\Model\DataType\OrderStatsDTO;
use Oxa\DfpBundle\Model\DataType\OrderStatsDTOCollection;
use StatementBuilder;
use DateTimeUtils;
use ReportQuery;
use ReportJob;
use ReportDownloader;

/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 26.08.16
 * Time: 15:18
 */
class ReportService
{
    const REPORT_SERVICE_NAME = 'ReportService';
    const API_VERSION = 'v201605';

    const REPORT_CLICKS_COL_NAME = 'AD_SERVER_CLICKS';
    const REPORT_IMPRESSIONS_COL_NAME = 'AD_SERVER_IMPRESSIONS';

    const CUSTOM_DATE_RANGE_TYPE = 'CUSTOM_DATE';

    const REPORT_RESPONSE_FORMAT = 'XML';
    const REPORT_RESPONSE_CLICKS_PARAMNAME = 'reservationClicksDelivered';
    const REPORT_RESPONSE_IMPRESSIONS_PARAMNAME = 'reservationImpressionsDelivered';
    const REPORT_RESPONSE_ORDER_PARAMNAME = 'orderId';

    /**
     * @var DfpUser
     */
    protected $dfpUser;

    /**
     * ReportService constructor.
     * @param DfpUser $dfpUser
     */
    public function __construct(DfpUser $dfpUser)
    {
        $this->dfpUser = $dfpUser;
    }

    /**
     * @param array $orderIds
     * @param DateRangeInterface $dateRange
     * @param array $columns
     * @return OrderStatsDTOCollection
     */
    public function getStatsForMultipleOrders(
        array $orderIds,
        DateRangeInterface $dateRange,
        array $columns
    ) : OrderStatsDTOCollection {
        $apiResponse = $this->fetchStats($orderIds, $dateRange, $columns);
        return new OrderStatsDTOCollection($apiResponse);
    }

    /**
     * @param int $orderId
     * @param DateRangeInterface $dateRange
     * @param array $columns
     * @return OrderStatsDTO
     */
    public function getStatsForSingleOrder(int $orderId, DateRangeInterface $dateRange, array $columns) : OrderStatsDTO
    {
        //normalize param
        $orderIds = [$orderId];

        $apiResponse = $this->fetchStats($orderIds, $dateRange, $columns);

        return new OrderStatsDTO($orderId, $apiResponse[$orderId]['clicks'], $apiResponse[$orderId]['impressions']);
    }

    /**
     * @param array $orderIds
     * @param DateRangeInterface $dateRange
     * @param array $columns
     * @return array
     */
    protected function fetchStats(array $orderIds, DateRangeInterface $dateRange, array $columns)
    {
        $user = $this->getDfpUser();

        $reportService = $user->GetService(self::REPORT_SERVICE_NAME, self::API_VERSION);

        // Create report query.
        $reportQuery = $this->prepareReportQuery($orderIds, $dateRange, $columns);

        // Create report job.
        $reportJob = new ReportJob();
        $reportJob->reportQuery = $reportQuery;

        // Run report job.
        $reportJob = $reportService->runReportJob($reportJob);

        // Create report downloader.
        $reportDownloader = new ReportDownloader($reportService, $reportJob->id);

        // Wait for the report to be ready.
        $reportDownloader->waitForReportReady();

        // Download the report.
        $xml = $reportDownloader->downloadReport(self::REPORT_RESPONSE_FORMAT);

        $preparedXML = $this->prepareXmlResponse($xml);

        return $preparedXML;
    }

    /**
     * @param string $gzippedXml
     * @return array
     */
    protected function prepareXmlResponse(string $gzippedXml) : array
    {
        $decodedXML = gzdecode($gzippedXml);
        $xml = simplexml_load_string($decodedXML);

        $prepared = [];

        foreach ($xml->ReportData->DataSet->Row as $row) {
            $order = 0;

            foreach ($row->Column as $column) {
                switch ((string)$column['name']) {
                    case self::REPORT_RESPONSE_ORDER_PARAMNAME:
                        $order = (string)$column->Val;
                        break;
                    case self::REPORT_RESPONSE_CLICKS_PARAMNAME:
                        $prepared[$order]['clicks'] = (int)$column->Val;
                        break;
                    case self::REPORT_RESPONSE_IMPRESSIONS_PARAMNAME:
                        $prepared[$order]['impressions'] = (int)$column->Val;
                        break;
                }
            }
        }

        return $prepared;
    }

    /**
     * @param array $orderIds
     * @param DateRangeInterface $dateRange
     * @param array $columns
     * @return ReportQuery
     */
    protected function prepareReportQuery(array $orderIds, DateRangeInterface $dateRange, array $columns) : ReportQuery
    {
        $reportQuery = new ReportQuery();

        $reportQuery->dimensions = ['ORDER_ID', 'ORDER_NAME'];
        $reportQuery->dimensionAttributes = [
            'ORDER_TRAFFICKER',
            'ORDER_START_DATE_TIME',
            'ORDER_END_DATE_TIME'
        ];

        $reportQuery->columns = $columns;

        // Create statement to filter for an order.
        $statementBuilder = new StatementBuilder();
        $statementBuilder->Where('order_id IN (' . implode(', ', $orderIds). ')');

        // Set the filter statement.
        $reportQuery->statement = $statementBuilder->ToStatement();

        // Set the start and end dates or choose a dynamic date range type.
        $reportQuery->dateRangeType = self::CUSTOM_DATE_RANGE_TYPE;

        $reportQuery->startDate = DateTimeUtils::ToDfpDateTime($dateRange->getStartDate())->date;
        $reportQuery->endDate = DateTimeUtils::ToDfpDateTime($dateRange->getEndDate())->date;

        return $reportQuery;
    }

    /**
     * @return DfpUser
     */
    protected function getDfpUser()
    {
        return $this->dfpUser;
    }
}
