<?php

namespace Oxa\DfpBundle\Manager;

use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\Util\v201911\ReportDownloader;
use Google\AdsApi\AdManager\v201911\Column;
use Google\AdsApi\AdManager\v201911\Dimension;
use Google\AdsApi\AdManager\v201911\ExportFormat;
use Google\AdsApi\AdManager\v201911\ReportJob;
use Google\AdsApi\AdManager\v201911\ReportQuery;
use Google\AdsApi\AdManager\v201911\ReportService;

class OrderReportManager
{
    public const DIMENSION_DATE                  = 0;
    public const DIMENSION_DEVICE_CATEGORY_NAME  = 1;
    public const DIMENSION_DEVICE_CATEGORY_ID    = 2;
    public const DIMENSION_ORDER_ID              = 3;

    public const COLUMN_CLICKS                   = 4;
    public const COLUMN_IMPRESSIONS              = 5;
    public const COLUMN_CTR                      = 6;

    private const TEMP_FILE_PREFIX = 'dcr';

    private const CSV_LINE_DELIMITER = "\n";
    private const CSV_ITEM_DELIMITER = ',';

    /* auth dfp session */
    protected $dfpSession;

    public function getOrderReportData($dfpSession, $period)
    {
        $this->dfpSession = $dfpSession;
        $reportFilePath = $this->downloadReport($period);
        $reportData     = $this->getReportDataFromFile($reportFilePath);

        return $reportData;
    }

    protected function getReportDataFromFile($reportFilePath)
    {
        $reportData = [];

        if ($reportFilePath) {
            $file = file_get_contents($reportFilePath);

            $csv = gzdecode($file);

            $rows = str_getcsv($csv, self::CSV_LINE_DELIMITER);

            $isFirst = true;

            foreach ($rows as $row) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                $item = str_getcsv($row, self::CSV_ITEM_DELIMITER);

                $reportData[] = [
                    $item[self::DIMENSION_DATE],
                    $item[self::DIMENSION_DEVICE_CATEGORY_NAME],
                    $item[self::DIMENSION_DEVICE_CATEGORY_ID],
                    $item[self::DIMENSION_ORDER_ID],
                    $item[self::COLUMN_CLICKS],
                    $item[self::COLUMN_IMPRESSIONS],
                    $item[self::COLUMN_CTR],
                ];
            }

            unlink($reportFilePath);
        }

        return $reportData;
    }

    protected function downloadReport($period)
    {
        $dfpServices = new AdManagerServices();

        $reportService = $dfpServices->get($this->dfpSession, ReportService::class);

        $reportJob = $this->getOrderReportJob($period);
        $reportJob = $reportService->runReportJob($reportJob);

        // Create report downloader to poll report's status and download when ready.
        $reportDownloader = new ReportDownloader($reportService, $reportJob->getId());

        if ($reportDownloader->waitForReportToFinish()) {
            // Write to system temp directory by default.
            $filePath = tempnam(sys_get_temp_dir(), self::TEMP_FILE_PREFIX);

            $reportDownloader->downloadReport(ExportFormat::CSV_DUMP, $filePath);
        } else {
            $filePath = null;
        }

        return $filePath;
    }

    protected function getOrderReportJob($period)
    {
        $reportQuery = new ReportQuery();

        $dimensions = [
            Dimension::DATE,
            Dimension::DEVICE_CATEGORY_NAME,
            Dimension::DEVICE_CATEGORY_ID,
            Dimension::ORDER_ID,
        ];

        $reportQuery->setDimensions($dimensions);

        $reportQuery->setColumns([
            Column::AD_SERVER_CLICKS,
            Column::AD_SERVER_IMPRESSIONS,
            Column::AD_SERVER_CTR,
        ]);

        $reportQuery->setDateRangeType($period);

        // Create report job and start it.
        $reportJob = new ReportJob();
        $reportJob->setReportQuery($reportQuery);

        return $reportJob;
    }
}
