<?php

namespace Oxa\DfpBundle\Manager;

use Google\AdsApi\Dfp\DfpServices;
use Google\AdsApi\Dfp\Util\v201702\ReportDownloader;
use Google\AdsApi\Dfp\v201702\Column;
use Google\AdsApi\Dfp\v201702\Dimension;
use Google\AdsApi\Dfp\v201702\ExportFormat;
use Google\AdsApi\Dfp\v201702\ReportJob;
use Google\AdsApi\Dfp\v201702\ReportQuery;
use Google\AdsApi\Dfp\v201702\ReportService;

class OrderReportManager
{
    const CSV_LINE_DELIMITER = "\n";
    const CSV_ITEM_DELIMITER = ',';

    const DIMENSION_DATE                  = 0;
    const DIMENSION_DEVICE_CATEGORY_NAME  = 1;
    const DIMENSION_DEVICE_CATEGORY_ID    = 2;
    const DIMENSION_ORDER_ID              = 3;

    const COLUMN_CLICKS                   = 4;
    const COLUMN_IMPRESSIONS              = 5;
    const COLUMN_CTR                      = 6;

    const TEMP_FILE_PREFIX = 'dcr';

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
        $dfpServices = new DfpServices();

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
