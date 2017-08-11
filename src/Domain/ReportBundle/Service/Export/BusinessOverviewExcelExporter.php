<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\ViewsAndVisitorsReportManager;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BusinessOverviewExcelExporter
 * @package Domain\ReportBundle\Export
 */
class BusinessOverviewExcelExporter extends ExcelExporterModel
{
    /**
     * @var ViewsAndVisitorsReportManager $viewsAndVisitorsReportManager
     */
    protected $viewsAndVisitorsReportManager;

    protected $mainTableInitRow = 9;
    protected $mainTableInitCol = 'B';

    /**
     * @param ViewsAndVisitorsReportManager $service
     */
    public function setViewsAndVisitorsReportManager(ViewsAndVisitorsReportManager $service)
    {
        $this->viewsAndVisitorsReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     * @throws \PHPExcel_Exception
     */
    public function getResponse($params = [])
    {
        $filename = $this->viewsAndVisitorsReportManager->generateReportName(self::FORMAT);

        $title = $this->translator->trans('export.title.business_overview_report', [], 'AdminReportBundle');

        return $this->sendDataResponse($params, $title, $filename);
    }

    /**
     * @param array $parameters
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    protected function setData($parameters)
    {
        $viewsAndVisitorsData = $this->viewsAndVisitorsReportManager->getViewsAndVisitorsData($parameters);

        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);

        $this->generateCommonHeader(current($viewsAndVisitorsData['dates']), end($viewsAndVisitorsData['dates']));
        $this->generateMainTable($viewsAndVisitorsData);

        return $this->phpExcelObject;
    }

    /**
     * @param array $overviewData
     */
    protected function generateMainTable($overviewData)
    {
        $row = $this->mainTableInitRow;
        $col = $this->mainTableInitCol;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('list.label_date', [], 'AdminReportBundle')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        foreach ($overviewData['mapping'] as $name) {
            $col++;
            $this->activeSheet->setCellValue(
                $col . $row,
                $this->translator->trans($name)
            );

            $this->setTextAlignmentStyle($col, $row);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
        }

        foreach ($overviewData['results'] as $rowData) {
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

        $col = $this->mainTableInitCol;
        $row++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('interaction_report.total')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        foreach ($overviewData['total'] as $item) {
            $col++;

            $this->activeSheet->setCellValue($col . $row, $item);

            $this->setColumnSizeStyle($col);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
        }

        $this->setRowSizeStyle($row);
    }
}
