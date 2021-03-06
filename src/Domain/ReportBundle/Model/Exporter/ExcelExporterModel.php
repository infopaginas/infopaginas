<?php

namespace Domain\ReportBundle\Model\Exporter;

use Doctrine\ORM\EntityManager;
use Domain\ReportBundle\Model\ExporterInterface;
use Liuggio\ExcelBundle\Factory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExcelExporterModel
 * @package Domain\ReportBundle\Model\Exporter
 */
abstract class ExcelExporterModel implements ExporterInterface
{
    protected const FORMAT = 'xls';
    protected const ROW_AUTO_HEIGHT = -1;
    protected const TITLE_MAX_LENGTH = 31;

    /**
     * @var Factory $phpExcel
     */
    protected $phpExcel;

    /**
     * @var Translator $translator
     */
    protected $translator;

    /**
     * @var \PHPExcel $phpExcelObject
     */
    protected $phpExcelObject;

    /**
     * @var \PHPExcel_Worksheet $activeSheet
     */
    protected $activeSheet;

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @param Factory $service
     */
    public function setPhpExcel(Factory $service)
    {
        $this->phpExcel = $service;
    }

    /**
     * @param Translator $service
     */
    public function setTranslator(Translator $service)
    {
        $this->translator = $service;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $col
     * @param int $row
     */
    protected function setFontStyle($col, $row)
    {
        $this->activeSheet
            ->getStyle($col . $row)
            ->applyFromArray($this->getFontStyle())
        ;
    }

    /**
     * @param string $col
     * @param int $row
     */
    protected function setTextAlignmentStyle($col, $row)
    {
        $this->activeSheet
            ->getStyle($col . $row)
            ->getAlignment()
            ->setWrapText(true)
        ;
    }

    /**
     * @param string $col
     * @param int $row
     */
    protected function setBorderStyle($col, $row)
    {
        $this->activeSheet
            ->getStyle($col . $row)
            ->applyFromArray($this->getBorderStyle())
        ;
    }

    /**
     * @param string $col
     */
    protected function setColumnSizeStyle($col)
    {
        $this->activeSheet
            ->getColumnDimension($col)
            ->setAutoSize(true)
        ;
    }

    /**
     * @param int $row
     */
    protected function setRowSizeStyle($row)
    {
        $this->activeSheet
            ->getRowDimension($row)
            ->setRowHeight(15)
        ;
    }

    /**
     * @param string $col
     * @param int $row
     */
    protected function setHeaderFontStyle($col, $row)
    {
        $this->activeSheet
            ->getStyle($col . $row)
            ->applyFromArray($this->getHeaderFontStyle())
        ;
    }

    /**
     * @return array
     */
    protected function getBorderStyle()
    {
        return [
            'borders' => [
                'allborders' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getFontStyle()
    {
        return [
            'font'  => [
                'bold'  => true,
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getHeaderFontStyle()
    {
        return [
            'borders' => [
                'allborders' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                ],
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ],
        ];
    }

    /**
     * @param string $startDate
     * @param string $endDate
     */
    protected function generateCommonHeader($startDate, $endDate)
    {
        $this->activeSheet->setCellValue(
            'B2',
            $this->translator->trans('export.generated_date', [], 'AdminReportBundle')
        );

        $this->activeSheet->mergeCells('B2:C2');

        $this->activeSheet->setCellValue(
            'B3',
            new \DateTime()
        );

        $this->activeSheet->mergeCells('B3:C3');

        // start date period
        $this->activeSheet->setCellValue(
            'B5',
            $this->translator->trans('export.date_period', [], 'AdminReportBundle')
        );

        $this->activeSheet->mergeCells('B5:C5');

        $this->activeSheet->setCellValue(
            'B6',
            $this->translator->trans('export.start_date', [], 'AdminReportBundle')
        );
        $this->activeSheet->setCellValue(
            'C6',
            $this->translator->trans('export.end_date', [], 'AdminReportBundle')
        );

        $this->activeSheet->setCellValue('B7', $startDate);
        $this->activeSheet->setCellValue('C7', $endDate);

        $this->setFontStyle('B', 2);
        $this->setFontStyle('B', 5);

        $this->setColumnSizeStyle('B');
        $this->setColumnSizeStyle('C');
        $this->setRowSizeStyle(2);
        $this->setRowSizeStyle(3);
        $this->setHeaderFontStyle('B', 2);
        $this->setHeaderFontStyle('B', 3);
        $this->setHeaderFontStyle('C', 2);
        $this->setHeaderFontStyle('C', 3);

        $this->setRowSizeStyle(5);
        $this->setRowSizeStyle(6);
        $this->setRowSizeStyle(5);
        $this->setHeaderFontStyle('B', 5);
        $this->setHeaderFontStyle('B', 6);
        $this->setHeaderFontStyle('B', 7);

        $this->setHeaderFontStyle('C', 5);
        $this->setHeaderFontStyle('C', 6);
        $this->setHeaderFontStyle('C', 7);
    }

    /**
     * @param array $params
     * @param string $title
     * @param string $filename
     *
     * @return Response
     */
    protected function sendDataResponse($params, $title, $filename)
    {
        $title = $this->getSafeTitle($title);

        $this->phpExcelObject = $this->phpExcel->createPHPExcelObject();
        $this->phpExcelObject = $this->setData($params);

        $this->phpExcelObject->getProperties()->setTitle($title);
        $this->phpExcelObject->getActiveSheet()->setTitle($title);

        return $this->sendResponse($filename);
    }

    /**
     * @param string $filename
     * @param string $path
     *
     * @return Response
     */
    protected function sendResponse($filename, $path = '')
    {
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->phpExcel->createWriter($this->phpExcelObject, 'Excel5');
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

        if ($path) {
            $this->phpExcel->createWriter($this->phpExcelObject)->save($path);
        }

        return $response;
    }

    /**
     * @param string $title
     *
     * @return string
     */
    protected function getSafeTitle($title)
    {
        $forbiddenChars= [
            '*',
            ':',
            '/',
            '\\',
            '?',
            '[',
            ']',
        ];

        return str_replace($forbiddenChars, '', $title);
    }
}
