<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\CategoryOverviewReportManager;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryExcelExporter
 * @package Domain\ReportBundle\Export
 */
class CategoryExcelExporter extends ExcelExporterModel
{
    /**
     * @var CategoryOverviewReportManager $categoryOverviewReportManager
     */
    protected $categoryOverviewReportManager;

    protected $mainTableInitRow = 9;
    protected $mainTableInitCol = 'B';

    /**
     * @param CategoryOverviewReportManager $service
     */
    public function setCategoryOverviewReportManager(CategoryOverviewReportManager $service)
    {
        $this->categoryOverviewReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     * @throws \PHPExcel_Exception
     */
    public function getResponse($params = [])
    {
        $filename = $this->categoryOverviewReportManager->generateReportName(self::FORMAT);

        $title = $this->translator->trans('export.title.category_report', [], 'AdminReportBundle');

        return $this->sendDataResponse($params, $title, $filename);
    }

    /**
     * @param array $filterParams
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    protected function setData(array $filterParams)
    {
        $categoryData = $this->categoryOverviewReportManager->getCategoryReportData($filterParams, false);

        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);

        $this->generateCommonHeader(
            $categoryData['dates']->getStartDate()->format(AdminHelper::DATE_FORMAT),
            $categoryData['dates']->getEndDate()->format(AdminHelper::DATE_FORMAT)
        );
        $this->generateMainTable($categoryData);

        return $this->phpExcelObject;
    }

    /**
     * @param array $categoryData
     */
    protected function generateMainTable($categoryData)
    {
        $row = $this->mainTableInitRow;
        $col = $this->mainTableInitCol;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('list.label_category', [], 'AdminReportBundle')
        );

        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        $col++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('list.label_impressions', [], 'AdminReportBundle')
        );

        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        $col++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('list.label_directions', [], 'AdminReportBundle')
        );

        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        $col++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('list.label_calls_mobile', [], 'AdminReportBundle')
        );

        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        $col++;
        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('list.label_category_visitors', [], 'AdminReportBundle')
        );

        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        foreach ($categoryData['results'] as $rowData) {
            $col = $this->mainTableInitCol;
            $row++;

            foreach ($rowData as $item) {
                $this->activeSheet->setCellValue($col . $row, $item);

                $this->setColumnSizeStyle($col);
                $this->setBorderStyle($col, $row);

                $col++;
            }

            $this->setRowSizeStyle($row);
        }
    }
}
