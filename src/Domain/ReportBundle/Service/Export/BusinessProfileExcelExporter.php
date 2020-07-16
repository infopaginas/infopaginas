<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Model\Exporter\ExcelPostponedExporterModel;

/**
 * Class BusinessProfileExcelExporter
 * @package Domain\ReportBundle\Export
 */
class BusinessProfileExcelExporter extends ExcelPostponedExporterModel
{
    /**
     * @var BusinessProfileManager $businessProfileManager
     */
    protected $businessProfileManager;

    protected $mainTableInitRow = 2;
    protected $mainTableInitCol = 'B';

    protected $reportTitle = 'export.title.businesses_report';

    /**
     * @param BusinessProfileManager $service
     */
    public function setBusinessProfileManager(BusinessProfileManager $service)
    {
        $this->businessProfileManager = $service;
    }

    /**
     * @param string $title
     * @param array  $parameters
     */
    protected function setData($title, $parameters = [])
    {
        $dataIterator = $this->businessProfileManager->getBusinessProfileExportDataIterator($parameters);

        $this->initProperties();

        foreach ($dataIterator as $item) {
            if ($this->isNewPage) {
                $this->createPHPExcelObject($title . $this->page);
                $path = $this->generateTempFilePath($parameters['exportPath'], $this->page);
                $this->generateHeaderTable(array_keys($item));

                $this->isNewPage = false;
            }

            $this->generateMainTable($item);
            $this->counter++;
            $this->em->clear();

            if ($this->counter >= self::MAX_ROW_PER_FILE) {
                $this->saveDataToFile($path);
                $this->isNewPage = true;
            }
        }

        // save last page
        if ($this->counter) {
            $this->saveDataToFile($path);
        }

        unset($dataIterator);
    }

    /**
     * @param array $data
     */
    protected function generateMainTable($data)
    {
        $this->currentCol = $this->mainTableInitCol;
        $this->currentRow++;

        foreach ($data as $value) {
            $this->activeSheet->setCellValue($this->currentCol . $this->currentRow, $value);

            $this->setColumnSizeStyle($this->currentCol);
            $this->setBorderStyle($this->currentCol, $this->currentRow);

            $this->currentCol++;
        }

        $this->setRowSizeStyle($this->currentRow);
    }

    /**
     * @param array $headers
     */
    protected function generateHeaderTable($headers)
    {
        $this->currentRow = $this->mainTableInitRow;
        $this->currentCol = $this->mainTableInitCol;

        $this->setFontStyle($this->currentCol, $this->currentRow);
        $this->setBorderStyle($this->currentCol, $this->currentRow);

        foreach ($headers as $name) {
            $this->activeSheet->setCellValue(
                $this->currentCol . $this->currentRow,
                $name
            );

            $this->setTextAlignmentStyle($this->currentCol, $this->currentRow);
            $this->setFontStyle($this->currentCol, $this->currentRow);
            $this->setBorderStyle($this->currentCol, $this->currentRow);
            $this->currentCol++;
        }
    }
}
