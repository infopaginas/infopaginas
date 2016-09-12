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
     * @param array $lineItemIds
     * @param DateRangeInterface $dateRange
     * @return \Oxa\DfpBundle\Model\DataType\OrderStatsDTOCollection
     */
    public function getStatsForMultipleLineItems(array $lineItemIds, DateRangeInterface $dateRange)
    {
        $columns = [ReportService::REPORT_CLICKS_COL_NAME, ReportService::REPORT_IMPRESSIONS_COL_NAME];
        return $this->getReportService()->getStatsForMultipleLineItems($lineItemIds, $dateRange, $columns);
    }

    /**
     * @return ReportService
     */
    protected function getReportService() : ReportService
    {
        return $this->reportService;
    }
}
