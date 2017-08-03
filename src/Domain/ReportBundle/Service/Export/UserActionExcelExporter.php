<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\UserActionReportManager;
use Domain\ReportBundle\Model\Exporter\ExcelPostponedExporterModel;
use Domain\ReportBundle\Model\UserActionModel;

/**
 * Class UserActionExcelExporter
 * @package Domain\ReportBundle\Export
 */
class UserActionExcelExporter extends ExcelPostponedExporterModel
{
    /**
     * @var UserActionReportManager $userActionReportManager
     */
    protected $userActionReportManager;

    protected $mainTableInitRow = 2;
    protected $mainTableInitCol = 'B';

    protected $reportTitle = 'export.title.user_action_report';

    /**
     * @param UserActionReportManager $service
     */
    public function setUserActionReportManager(UserActionReportManager $service)
    {
        $this->userActionReportManager = $service;
    }

    /**
     * @param string $title
     * @param array  $parameters
     */
    protected function setData($title, $parameters = [])
    {
        $dataIterator = $this->userActionReportManager->getUserActionReportExportDataIterator($parameters);
        $headers = UserActionReportManager::getUserActionReportMapping();

        $this->initProperties();

        foreach ($dataIterator as $item) {
            if ($this->isNewPage) {
                $this->createPHPExcelObject($title . $this->page);
                $path = $this->generateTempFilePath($parameters['exportPath'], $this->page);
                $this->generateHeaderTable($headers);

                $this->isNewPage = false;
            }

            $this->generateMainTable($item);
            $this->counter++;

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
     * @param array $rawData
     */
    protected function generateMainTable($rawData)
    {
        $data = $this->userActionReportManager->convertMongoDataToArray($rawData);

        $eventsMapping = UserActionModel::EVENT_TYPES;

        $this->currentCol = $this->mainTableInitCol;
        $this->currentRow++;

        foreach ($data as $key => $value) {
            if ($key == UserActionReportManager::MONGO_DB_FIELD_DATA) {
                $info = implode(PHP_EOL, $value);
                $this->activeSheet->setCellValue($this->currentCol . $this->currentRow, $info);
                $this->activeSheet->getRowDimension($this->currentRow)->setRowHeight(self::ROW_AUTO_HEIGHT);
                $this->activeSheet->getStyle($this->currentCol . $this->currentRow)->getAlignment()->setWrapText(true);
            } elseif ($key == UserActionReportManager::MONGO_DB_FIELD_ACTION) {
                $this->activeSheet->setCellValue(
                    $this->currentCol . $this->currentRow,
                    $this->translator->trans($eventsMapping[$value], [], 'AdminReportBundle')
                );
            } elseif ($key == UserActionReportManager::MONGO_DB_FIELD_ENTITY_NAME and !$value) {
                $this->activeSheet->setCellValue(
                    $this->currentCol . $this->currentRow,
                    $this->translator->trans('user_action_report.no_associated_entity', [], 'AdminReportBundle')
                );
            } else {
                $this->activeSheet->setCellValue($this->currentCol . $this->currentRow, $value);
            }

            $this->setColumnSizeStyle($this->currentCol);
            $this->setBorderStyle($this->currentCol, $this->currentRow);

            $this->currentCol++;
        }
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
                $this->translator->trans($name, [], 'AdminReportBundle')
            );

            $this->setTextAlignmentStyle($this->currentCol, $this->currentRow);
            $this->setFontStyle($this->currentCol, $this->currentRow);
            $this->setBorderStyle($this->currentCol, $this->currentRow);
            $this->currentCol++;
        }
    }
}
