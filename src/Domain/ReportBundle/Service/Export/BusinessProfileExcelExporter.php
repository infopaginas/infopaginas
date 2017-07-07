<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;

/**
 * Class BusinessProfileExcelExporter
 * @package Domain\ReportBundle\Export
 */
class BusinessProfileExcelExporter extends ExcelExporterModel
{
    /**
     * @var BusinessProfileManager $businessProfileManager
     */
    protected $businessProfileManager;

    protected $mainTableInitRow = 2;
    protected $mainTableInitCol = 'B';

    /**
     * @param BusinessProfileManager $service
     */
    public function setBusinessProfileManager(BusinessProfileManager $service)
    {
        $this->businessProfileManager = $service;
    }

    /**
     * @param array $parameters
     * @return Array
     * @throws \PHPExcel_Exception
     */
    public function getResponse($parameters = [])
    {
        $title = $this->translator->trans('export.title.businesses_report', [], 'AdminReportBundle');
        $title = $this->getSafeTitle($title);

        $data = $this->businessProfileManager->getBusinessProfileExportData($parameters);

        $files = [];

        foreach ($data as $page) {
            $path = $this->generateTempFilePath($parameters['exportPath']);

            $this->phpExcelObject = $this->phpExcel->createPHPExcelObject();
            $this->phpExcelObject = $this->setData($page);

            $this->phpExcelObject->getProperties()->setTitle($title);
            $this->phpExcelObject->getActiveSheet()->setTitle($title);

            $status = $this->saveResponse($path);

            if ($status) {
                $files[] = $path;
            }
        }

        unset($data);

        return $files;
    }

    /**
     * @param array $data
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    protected function setData($data)
    {
        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);
        $this->generateMainTable($data);

        return $this->phpExcelObject;
    }

    protected function generateMainTable($data)
    {
        $row = $this->mainTableInitRow;
        $col = $this->mainTableInitCol;

        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        $mapping = array_keys(current($data));

        foreach ($mapping as $name) {
            $this->activeSheet->setCellValue(
                $col . $row,
                $name
            );

            $this->setTextAlignmentStyle($col, $row);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
            $col++;
        }

        foreach ($data as $rowData) {
            $col = $this->mainTableInitCol;
            $row++;

            foreach ($rowData as $key => $value) {
                $this->activeSheet->setCellValue($col . $row, $value);

                $this->setColumnSizeStyle($col);
                $this->setBorderStyle($col, $row);

                $col++;
            }

            $this->setRowSizeStyle($row);
        }
    }
}
