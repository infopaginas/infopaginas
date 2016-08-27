<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 25.08.16
 * Time: 21:58
 */

namespace Oxa\DfpBundle\Manager;

use Oxa\DfpBundle\Model\DataType\DateRangeInterface;
use Oxa\DfpBundle\Model\DataType\OrderStatsDTO;
use Oxa\DfpBundle\Service\Google\ReportService;

/**
 * Class DfpManager
 * @package Oxa\DfpBundle\Manager
 */
class DfpManager
{
    /**
     * @var ReportService
     */
    protected $reportService;

    /**
     * DfpManager constructor.
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @param int $orderId
     * @param DateRangeInterface $dateRange
     * @return OrderStatsDTO
     */
    public function getStatsForSingleOrder(int $orderId, DateRangeInterface $dateRange) : OrderStatsDTO
    {
        $columns = [ReportService::REPORT_CLICKS_COL_NAME, ReportService::REPORT_IMPRESSIONS_COL_NAME];
        return $this->getReportService()->getStatsForSingleOrder($orderId, $dateRange, $columns);
    }

    public function getStatsForMultipleOrders(array $orderIds, DateRangeInterface $dateRange)
    {
        $columns = [ReportService::REPORT_CLICKS_COL_NAME, ReportService::REPORT_IMPRESSIONS_COL_NAME];
        return $this->getReportService()->getStatsForMultipleOrders($orderIds, $dateRange, $columns);
    }

    /**
     * @return ReportService
     */
    protected function getReportService() : ReportService
    {
        return $this->reportService;
    }
}
