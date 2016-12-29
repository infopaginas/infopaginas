<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/13/16
 * Time: 7:57 PM
 */

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class BusinessOverviewExcelExporter
 * @package Domain\ReportBundle\Export
 */
class BusinessOverviewExcelExporter extends ExcelExporterModel
{
    /**
     * @var EngineInterface $templateEngine
     */
    protected $templateEngine;

    /**
     * @var BusinessOverviewReportManager $businessOverviewReportManager
     */
    protected $businessOverviewReportManager;

    /**
     * @param BusinessOverviewReportManager $service
     */
    public function setBusinessOverviewReportManager(BusinessOverviewReportManager $service)
    {
        $this->businessOverviewReportManager = $service;
    }

    /**
     * @param string $code
     * @param string $format
     * @param array $filterParams
     * @param array $params
     * @return Response
     * @throws \PHPExcel_Exception
     */
    public function getResponse(string $code, string $format, array $filterParams, $params = []) : Response
    {
        list($businessOverviewData, $filename) =
            $this->businessOverviewReportManager->getBusinessOverviewReportDataAndName($filterParams, $format);

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()
            ->setTitle($this->translator->trans('export.title.business_overview_report', [], 'AdminReportBundle'))
        ;

        $phpExcelObject = $this->setData($phpExcelObject, $filterParams, $businessOverviewData);

        $phpExcelObject->getActiveSheet()
            ->setTitle(
                $this->translator->trans('export.title.active_sheet', [], 'AdminReportBundle')
            );
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->phpExcel->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @param \PHPExcel $phpExcelObject
     * @param array $filterParams
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    public function setData(\PHPExcel $phpExcelObject, array $filterParams, array $data)
    {
        $activeSheet = $phpExcelObject->setActiveSheetIndex(0);

        // title part
        if (!empty($data['businessProfile'])) {
            $business = $data['businessProfile'];
        } else {
            $business = $this->translator->trans('export.title.all_businesses', [], 'AdminReportBundle');
        }

        // generated date
        $activeSheet->setCellValue(
            'B2',
            $this->translator->trans('export.generated_date', [], 'AdminReportBundle')
        );

        $activeSheet->setCellValue(
            'B3',
            new \DateTime()
        );

        $activeSheet->setCellValue(
            'B5',
            $this->translator->trans('export.title.business_overview_report', [], 'AdminReportBundle')
        );

        $activeSheet->setCellValue(
            'B6',
            $business
        );
        // end title part

        // start date period
        $activeSheet->setCellValue(
            'B8',
            $this->translator->trans('export.date_period', [], 'AdminReportBundle')
        );

        // merge cells to make them more friendly
        $activeSheet->mergeCells('B2:C2');
        $activeSheet->mergeCells('B3:C3');
        $activeSheet->mergeCells('B5:C5');
        $activeSheet->mergeCells('B6:C6');
        $activeSheet->mergeCells('B8:C8');

        $activeSheet->setCellValue(
            'B9',
            $this->translator->trans('export.start_date', [], 'AdminReportBundle')
        );
        $activeSheet->setCellValue(
            'C9',
            $this->translator->trans('export.end_date', [], 'AdminReportBundle')
        );

        $activeSheet->setCellValue(
            'B10',
            $data['datePeriod']['start']
        );
        $activeSheet->setCellValue(
            'C10',
            $data['datePeriod']['end']
        );
        // end date period

        $cell = $initCell = 12;
        $row = $initRow = $maxRow = 'B';

        // start header
        $activeSheet->setCellValue(
            $row.$cell,
            $this->translator->trans('list.label_date', [], 'AdminReportBundle')
        );

        ++$row;
        $activeSheet->setCellValue(
            $row.$cell,
            $this->translator->trans('list.label_impressions', [], 'AdminReportBundle')
        );

        ++$row;
        $activeSheet->setCellValue(
            $row.$cell,
            $this->translator->trans('list.label_views', [], 'AdminReportBundle')
        );
        // end header

        // start main data (categoryName and visitors)
        foreach ($data['results'] as $element) {
            $row = $initRow;

            ++$cell;
            $activeSheet->setCellValue($row.$cell, $element['date']);

            ++$row;
            $activeSheet->setCellValue(
                $row.$cell,
                $element['impressions']
            );

            ++$row;
            $activeSheet->setCellValue(
                $row.$cell,
                $element['views']
            );

            if ($row > $maxRow) {
                $maxRow = $row;
            }
        }
        // end main data (categoryName and visitors)


        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $fontStyleArray = array(
            'font'  => array(
                'bold'  => true,
            ));

        // apply styles
        $maxRow++;
        $cell++;
        // to main table
        for ($r = $initRow; $r < $maxRow; $r++) {
            for ($c = $initCell; $c < $cell; $c++) {
                $activeSheet
                    ->getColumnDimension($r)
                    ->setAutoSize(true)
                ;

                $activeSheet
                    ->getStyle($r.$c)
                    ->applyFromArray($styleArray)
                ;

                $activeSheet
                    ->getRowDimension($c)
                    ->setRowHeight(15)
                ;


                // set font weight as bold to the last line
                if ($c == $initCell) {
                    $activeSheet
                        ->getStyle($r.$c)
                        ->applyFromArray($fontStyleArray)
                    ;
                }
            }
        }

        // to header table
        $textAlignStyleArray = [
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        ];

        $styleArray = array_merge($styleArray, $textAlignStyleArray);

        // make some headers text bolder
        $activeSheet
            ->getStyle('B2')
            ->applyFromArray($fontStyleArray)
        ;

        $activeSheet
            ->getStyle('B5')
            ->applyFromArray($fontStyleArray)
        ;

        $activeSheet
            ->getStyle('B8')
            ->applyFromArray($fontStyleArray)
        ;

        // apply styles for header
        for ($r = 'B'; $r < 'D'; $r++) {
            for ($c = 2; $c < 4; $c++) {
                $activeSheet
                    ->getColumnDimension($r)
                    ->setAutoSize(true)
                ;

                $activeSheet
                    ->getStyle($r.$c)
                    ->applyFromArray($styleArray)
                ;

                $activeSheet
                    ->getRowDimension($c)
                    ->setRowHeight(15)
                ;
            }
        }

        // apply styles for header
        for ($r = 'B'; $r < 'D'; $r++) {
            for ($c = 5; $c < 7; $c++) {
                $activeSheet
                    ->getColumnDimension($r)
                    ->setAutoSize(true)
                ;

                $activeSheet
                    ->getStyle($r.$c)
                    ->applyFromArray($styleArray)
                ;

                $activeSheet
                    ->getRowDimension($c)
                    ->setRowHeight(15)
                ;
            }
        }

        // apply styles for date period block
        for ($r = 'B'; $r < 'D'; $r++) {
            for ($c = 8; $c < 11; $c++) {
                $activeSheet
                    ->getColumnDimension($r)
                    ->setAutoSize(true)
                ;

                $activeSheet
                    ->getStyle($r.$c)
                    ->applyFromArray($styleArray)
                ;

                $activeSheet
                    ->getRowDimension($c)
                    ->setRowHeight(15)
                ;
            }
        }

        return $phpExcelObject;
    }
}
