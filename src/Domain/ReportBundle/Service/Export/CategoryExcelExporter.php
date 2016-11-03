<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 7/13/16
 * Time: 7:57 PM
 */

namespace Domain\ReportBundle\Service\Export;

use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class CategoryExcelExporter
 * @package Domain\ReportBundle\Export
 */
class CategoryExcelExporter extends ExcelExporterModel
{
    /**
     * @var EngineInterface $templateEngine
     */
    protected $templateEngine;

    /**
     * @var CategoryReportManager $categoryReportManager
     */
    protected $categoryReportManager;

    /**
     * @param CategoryReportManager $service
     */
    public function setCategoryReportManager(CategoryReportManager $service)
    {
        $this->categoryReportManager = $service;
    }

    /**
     * @param string $code
     * @param string $format
     * @param array $filterParams
     * @return Response
     * @throws \PHPExcel_Exception
     */
    public function getResponse(string $code, string $format, array $filterParams) : Response
    {
        $filename = $this->categoryReportManager->generateReportName($format, 'category_report');

        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()
            ->setTitle($this->translator->trans('export.title.category_report', [], 'AdminReportBundle'))
        ;

        $phpExcelObject = $this->setData($phpExcelObject, $filterParams);

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
    public function setData(\PHPExcel $phpExcelObject, array $filterParams)
    {
        // count data
        $categoryData = $this->categoryReportManager
            ->getCategoryVisitorsQuantitiesByFilterParams($filterParams);

        $activeSheet = $phpExcelObject->setActiveSheetIndex(0);

        // generated date
        $activeSheet->setCellValue(
            'B2',
            $this->translator->trans('export.generated_date', [], 'AdminReportBundle')
        );

        $activeSheet->mergeCells('B2:C2');

        $activeSheet->setCellValue(
            'B3',
            new \DateTime()
        );

        $activeSheet->mergeCells('B3:C3');

        // start date period
        $activeSheet->setCellValue(
            'B5',
            $this->translator->trans('export.date_period', [], 'AdminReportBundle')
        );

        $activeSheet->mergeCells('B5:C5');

        $activeSheet->setCellValue(
            'B6',
            $this->translator->trans('export.start_date', [], 'AdminReportBundle')
        );
        $activeSheet->setCellValue(
            'C6',
            $this->translator->trans('export.end_date', [], 'AdminReportBundle')
        );

        $activeSheet->setCellValue(
            'B7',
            $categoryData['datePeriod']['start']
        );
        $activeSheet->setCellValue(
            'C7',
            $categoryData['datePeriod']['end']
        );
        // end date period

        $cell = $initCell = 9;
        $row = $initRow = $maxRow = 'B';

        // start header
        $activeSheet->setCellValue(
            $row.$cell,
            $this->translator->trans('list.label_category', [], 'AdminReportBundle')
        );

        ++$row;
        $activeSheet->setCellValue(
            $row.$cell,
            $this->translator->trans('list.label_category_visitors', [], 'AdminReportBundle')
        );
        // end header

        // start main data (categoryName and visitors)
        foreach ($categoryData['resultsArray'] as $element) {
            $row = $initRow;

            ++$cell;
            $activeSheet->setCellValue($row.$cell, $element['categoryName']);

            ++$row;
            $activeSheet->setCellValue(
                $row.$cell,
                $element['categoryVisitors']
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

        $activeSheet
            ->getStyle('B2')
            ->applyFromArray($fontStyleArray)
        ;
        $activeSheet
            ->getStyle('B5')
            ->applyFromArray($fontStyleArray)
        ;

        for ($r = 'B'; $r < 'D'; $r++) {
            for ($c = 5; $c < 8; $c++) {
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

        return $phpExcelObject;
    }
}
