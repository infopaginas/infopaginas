<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\CategoryReportManager;
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
     * @var CategoryReportManager $categoryReportManager
     */
    protected $categoryReportManager;

    protected $mainTableInitRow = 9;
    protected $mainTableInitCol = 'B';

    /**
     * @param CategoryReportManager $service
     */
    public function setCategoryReportManager(CategoryReportManager $service)
    {
        $this->categoryReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     * @throws \PHPExcel_Exception
     */
    public function getResponse($params = [])
    {
        $filename = $this->categoryReportManager->generateReportName(self::FORMAT);

        $title = $this->translator->trans('export.title.category_report', [], 'AdminReportBundle');
        $title = $this->getSafeTitle($title);

        $this->phpExcelObject = $this->phpExcel->createPHPExcelObject();
        $this->phpExcelObject = $this->setData($params);

        $this->phpExcelObject->getProperties()->setTitle($title);
        $this->phpExcelObject->getActiveSheet()->setTitle($title);

        return $this->sendResponse($filename);
    }

    /**
     * @param array $filterParams
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    protected function setData(array $filterParams)
    {
        $categoryData = $this->categoryReportManager->getCategoryReportData($filterParams);

        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);

        $this->generateCommonHeader(
            $categoryData['dates']->getStartDate()->format(AdminHelper::DATE_FORMAT),
            $categoryData['dates']->getEndDate()->format(AdminHelper::DATE_FORMAT)
        );
        $this->generateMainTable($categoryData);

        return $this->phpExcelObject;
    }

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
