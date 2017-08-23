<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Component\HttpFoundation\Response;

class BusinessReportExcelExporter extends ExcelExporterModel
{
    const TITLE_MAX_LENGTH = 31;

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

    protected $mainTableInitRow         = 9;
    protected $mainTableInitCol         = 'B';

    protected $currentOverviewInitRow   = 9;
    protected $currentOverviewInitCol   = 'B';

    protected $keywordsTableInitRow     = 9;
    protected $keywordsTableInitCol     = 'F';

    protected $currentYearInitRow       = 9;
    protected $currentYearInitCol       = 'I';

    protected $previousYearInitRow      = 9;
    protected $previousYearInitCol      = 'L';

    protected $adUsageInitRow           = 9;
    protected $adUsageInitCol           = 'P';

    protected $interactionInitRow       = 9;
    protected $interactionInitCol       = 'V';

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
     * @throws \PHPExcel_Exception
     */
    public function getResponse($params = [])
    {
        /* @var BusinessProfile $businessProfile */
        $businessProfile = $params['businessProfile'];

        $filename = $this->businessOverviewReportManager
            ->getBusinessOverviewReportName($businessProfile->getSlug(), self::FORMAT);

        $title = mb_substr($businessProfile->getName(), 0, self::TITLE_MAX_LENGTH);

        return $this->sendDataResponse($params, $title, $filename);
    }

    /**
     * @param array $parameters
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    protected function setData($parameters)
    {
        $currentYearParams  = $this->businessOverviewReportManager->getThisYearSearchParams($parameters);
        $previousYearParams = $this->businessOverviewReportManager->getThisLastSearchParams($parameters);

        $interactionCurrentData  = $this->businessOverviewReportManager->getBusinessOverviewReportData($parameters);

        $keywordsData = $this->keywordsReportManager->getKeywordsData($parameters);

        $interactionCurrentYearData  = $this->businessOverviewReportManager
            ->getBusinessOverviewReportData($currentYearParams);
        $interactionPreviousYearData = $this->businessOverviewReportManager
            ->getBusinessOverviewReportData($previousYearParams);

        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);

        $this->generateCommonHeader(
            current($interactionCurrentData['dates']),
            end($interactionCurrentData['dates'])
        );
        $this->generateTotalTable($interactionCurrentData['total']);
        $this->generateBusinessInfoTable($parameters['businessProfile']);

        $this->generateCurrentOverviewTable($interactionCurrentData);
        $this->generateKeywordsTable($keywordsData);
        $this->generateYearTable(
            [
                'data'      => $interactionCurrentYearData,
                'title'     => 'Current Year',
                'initRow'   => $this->currentYearInitRow,
                'initCol'   => $this->currentYearInitCol,
            ]
        );
        $this->generateYearTable(
            [
                'data'      => $interactionPreviousYearData,
                'title'     => 'Previous Year',
                'initRow'   => $this->previousYearInitRow,
                'initCol'   => $this->previousYearInitCol,
            ]
        );

        if ($parameters['businessProfile']->getDcOrderId()) {
            $adUsageData = $this->adUsageReportManager->getAdUsageData($parameters);
            $this->generateAdUsageTable($adUsageData);
        } else {
            $this->interactionInitCol = $this->adUsageInitCol;
            $this->interactionInitRow = $this->adUsageInitRow;
        }

        $this->generateInteractionTable($interactionCurrentData);

        return $this->phpExcelObject;
    }

    /**
     * @param array $total
     */
    protected function generateTotalTable($total)
    {
        $this->activeSheet->setCellValue('E6', 'Total Profile Impressions');
        $this->activeSheet->setCellValue('F6', $total[BusinessOverviewModel::TYPE_CODE_IMPRESSION]);
        $this->activeSheet->setCellValue('E7', 'Total Profile Views');
        $this->activeSheet->setCellValue('F7', $total[BusinessOverviewModel::TYPE_CODE_VIEW]);

        $this->setFontStyle('E', 6);
        $this->setFontStyle('E', 7);

        $this->setHeaderFontStyle('E', 6);
        $this->setHeaderFontStyle('E', 7);

        $this->setHeaderFontStyle('F', 6);
        $this->setHeaderFontStyle('F', 7);
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    protected function generateBusinessInfoTable(BusinessProfile $businessProfile)
    {
        $this->activeSheet->setCellValue('E2', 'Name');
        $this->activeSheet->setCellValue('F2', $businessProfile->getName());
        $this->activeSheet->setCellValue('E3', 'Address');
        $this->activeSheet->setCellValue('F3', $businessProfile->getFullAddress());

        $this->setFontStyle('E', 2);
        $this->setFontStyle('E', 3);

        $this->setHeaderFontStyle('E', 2);
        $this->setHeaderFontStyle('E', 3);

        $this->setHeaderFontStyle('F', 2);
        $this->setHeaderFontStyle('F', 3);
    }

    /**
     * @param array $interactionCurrentData
     */
    protected function generateCurrentOverviewTable($interactionCurrentData)
    {
        $row = $this->currentOverviewInitRow;
        $col = $this->currentOverviewInitCol;

        $this->activeSheet->setCellValue($col . $row, 'Date');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue($col . $row, 'Impressions');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue($col . $row, 'Views');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $row++;

        foreach ($interactionCurrentData['results'] as $overview) {
            $col = $this->currentOverviewInitCol;
            $this->activeSheet->setCellValue($col . $row, $overview['date']);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $col++;
            $this->activeSheet->setCellValue($col . $row, $overview[BusinessOverviewModel::TYPE_CODE_IMPRESSION]);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $col++;
            $this->activeSheet->setCellValue($col . $row, $overview[BusinessOverviewModel::TYPE_CODE_VIEW]);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $row++;
        }
    }

    /**
     * @param array $interactionData
     */
    protected function generateInteractionTable($interactionData)
    {
        $row = $this->interactionInitRow;
        $col = $this->interactionInitCol;

        $mapping = BusinessOverviewModel::EVENT_TYPES;

        foreach (current($interactionData['results']) as $key => $item) {
            if (!empty($mapping[$key])) {
                $label = $mapping[$key];
            } else {
                $label = $key;
            }

            $this->activeSheet->setCellValue(
                $col . $row,
                $this->translator->trans($label)
            );

            $this->setTextAlignmentStyle($col, $row);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
            $col++;
        }

        foreach ($interactionData['results'] as $rowData) {
            $col = $this->interactionInitCol;
            $row++;

            foreach ($rowData as $item) {
                $this->activeSheet->setCellValue($col . $row, $item);

                $this->setColumnSizeStyle($col);
                $this->setBorderStyle($col, $row);

                $col++;
            }

            $this->setRowSizeStyle($row);
        }

        $col = $this->interactionInitCol;
        $row++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('interaction_report.total')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        foreach ($interactionData['total'] as $item) {
            $col++;

            $this->activeSheet->setCellValue($col . $row, $item);

            $this->setColumnSizeStyle($col);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
        }

        $this->setRowSizeStyle($row);
    }

    /**
     * @param array $keywordsData
     */
    protected function generateKeywordsTable($keywordsData)
    {
        $row = $this->keywordsTableInitRow;
        $col = $this->keywordsTableInitCol;

        $this->activeSheet->setCellValue($col . $row, 'Keyword');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue($col . $row, 'Number of searches');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $row++;

        foreach ($keywordsData['results'] as $keyword => $searches) {
            $col = $this->keywordsTableInitCol;
            $this->activeSheet->setCellValue($col . $row, $keyword);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $col++;
            $this->activeSheet->setCellValue($col . $row, $searches);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $row++;
        }
    }

    /**
     * @param array $params
     */
    protected function generateYearTable($params)
    {
        $row = $params['initRow'];
        $col = $params['initCol'];

        $this->activeSheet->setCellValue($col . $row, $params['title']);
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue($col . $row, 'Impressions');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue($col . $row, 'Views');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $row++;

        foreach ($params['data']['results'] as $overview) {
            $col = $params['initCol'];

            $this->activeSheet->setCellValue(
                $col . $row,
                DatesUtil::convertMonthlyFormattedDate($overview['date'], AdminHelper::DATE_FULL_MONTH_FORMAT)
            );

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $col++;
            $this->activeSheet->setCellValue($col . $row, $overview[BusinessOverviewModel::TYPE_CODE_IMPRESSION]);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $col++;
            $this->activeSheet->setCellValue($col . $row, $overview[BusinessOverviewModel::TYPE_CODE_VIEW]);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $row++;
        }
    }

    /**
     * @param array $adUsageData
     */
    protected function generateAdUsageTable($adUsageData)
    {
        $row = $this->adUsageInitRow;
        $col = $this->adUsageInitCol;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('list.label_date', [], 'AdminReportBundle')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('ad_usage_report.device_category')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('ad_usage_report.clicks')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('ad_usage_report.impressions')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('ad_usage_report.ctr')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        foreach ($adUsageData['dates'] as $key => $date) {
            foreach ($adUsageData['deviceCategories'] as $id => $category) {
                $col = $this->adUsageInitCol;
                $row++;

                foreach ($adUsageData['results'][$id][$key] as $item) {
                    $this->activeSheet->setCellValue($col . $row, $this->translator->trans($item));

                    $this->setColumnSizeStyle($col);
                    $this->setBorderStyle($col, $row);

                    $col++;
                }

                $this->setRowSizeStyle($row);
            }
        }

        $col = $this->adUsageInitCol;
        $row++;
        $col++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('ad_usage_report.total')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        foreach ($adUsageData['total'] as $item) {
            $col++;

            $this->activeSheet->setCellValue($col . $row, $item);

            $this->setColumnSizeStyle($col);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
        }

        $this->setRowSizeStyle($row);
    }
}
