<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\SubscriptionReportManager;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SubscriptionExcelExporter
 * @package Domain\ReportBundle\Export
 */
class SubscriptionExcelExporter extends ExcelExporterModel
{
    /**
     * @var SubscriptionReportManager $subscriptionReportManager
     */
    protected $subscriptionReportManager;

    protected $mainTableInitRow = 9;
    protected $mainTableInitCol = 'B';

    /**
     * @param SubscriptionReportManager $service
     */
    public function setSubscriptionReportManager(SubscriptionReportManager $service)
    {
        $this->subscriptionReportManager = $service;
    }

    /**
     * @param array $parameters
     * @return Response
     * @throws \PHPExcel_Exception
     */
    public function getResponse($parameters = [])
    {
        $filename = $this->subscriptionReportManager->generateReportName(self::FORMAT);

        $title = $this->translator->trans('export.title.subscription_report', [], 'AdminReportBundle');
        $title = $this->getSafeTitle($title);

        $this->phpExcelObject = $this->phpExcel->createPHPExcelObject();
        $this->phpExcelObject = $this->setData($parameters);

        $this->phpExcelObject->getProperties()->setTitle($title);
        $this->phpExcelObject->getActiveSheet()->setTitle($title);

        return $this->sendResponse($filename);
    }

    /**
     * @param array $parameters
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    protected function setData($parameters)
    {
        $subscriptionData = $this->subscriptionReportManager->getSubscriptionsReportData($parameters);

        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);

        $this->generateCommonHeader(current($subscriptionData['dates']), end($subscriptionData['dates']));
        $this->generateMainTable($subscriptionData);

        return $this->phpExcelObject;
    }

    /**
     * @param array $subscriptionData
     */
    protected function generateMainTable($subscriptionData)
    {
        $row = $this->mainTableInitRow;
        $col = $this->mainTableInitCol;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('list.label_date', [], 'AdminReportBundle')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        foreach ($subscriptionData['mapping'] as $name) {
            $col++;
            $this->activeSheet->setCellValue(
                $col . $row,
                $this->translator->trans($name)
            );

            $this->setTextAlignmentStyle($col, $row);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
        }

        foreach ($subscriptionData['results'] as $rowData) {
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
