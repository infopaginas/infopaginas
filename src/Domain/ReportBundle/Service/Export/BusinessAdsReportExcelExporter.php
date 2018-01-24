<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;
use Symfony\Component\HttpFoundation\Response;

class BusinessAdsReportExcelExporter extends ExcelExporterModel
{
    /**
     * @var BusinessOverviewReportManager $businessOverviewReportManager
     */
    protected $businessOverviewReportManager;

    /**
     * @var AdUsageReportManager $adUsageReportManager
     */
    protected $adUsageReportManager;

    protected $mainTableInitRow = 9;
    protected $mainTableInitCol = 'B';

    protected $adUsageInitRow = 9;
    protected $adUsageInitCol = 'B';

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
        $interactionCurrentData  = $this->businessOverviewReportManager->getBusinessOverviewReportData($parameters);

        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);

        $this->generateCommonHeader(
            current($interactionCurrentData['dates']),
            end($interactionCurrentData['dates'])
        );
        $this->generateBusinessInfoTable($parameters['businessProfile']);

        $adUsageData = $this->adUsageReportManager->getAdUsageData($parameters);
        $this->generateAdUsageTable($adUsageData);

        return $this->phpExcelObject;
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
