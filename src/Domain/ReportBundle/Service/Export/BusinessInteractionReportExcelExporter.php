<?php

namespace Domain\ReportBundle\Service\Export;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\Exporter\ExcelExporterModel;
use Symfony\Component\HttpFoundation\Response;

class BusinessInteractionReportExcelExporter extends ExcelExporterModel
{
    /**
     * @var BusinessOverviewReportManager $businessOverviewReportManager
     */
    protected $businessOverviewReportManager;

    /**
     * @var KeywordsReportManager $keywordsReportManager
     */
    protected $keywordsReportManager;

    protected $mainTableInitRow = 9;
    protected $mainTableInitCol = 'B';

    protected $keywordsTableInitRow = 9;
    protected $keywordsTableInitCol = 'B';

    protected $interactionInitRow = 9;
    protected $interactionInitCol = 'E';

    /**
     * @param BusinessOverviewReportManager $service
     */
    public function setBusinessOverviewReportManager(BusinessOverviewReportManager $service)
    {
        $this->businessOverviewReportManager = $service;
    }

    /**
     * @param KeywordsReportManager $service
     */
    public function setKeywordsReportManager(KeywordsReportManager $service)
    {
        $this->keywordsReportManager = $service;
    }

    /**
     * @param array $params
     * @return Response
     * @throws \PHPExcel_Exception
     */
    public function getResponse($params = [])
    {
        /* @var BusinessProfile $businessProfile */
        $businessProfile = $params['businessProfile'];

        $filename = $this->businessOverviewReportManager
            ->getBusinessOverviewReportName($businessProfile->getSlug(), self::FORMAT);

        $title = mb_substr($businessProfile->getName(), 0, self::TITLE_MAX_LENGTH);

        return $this->sendDataResponse($params, $title, $filename);
    }

    /**
     * @param array $parameters
     * @return \PHPExcel
     * @throws \PHPExcel_Exception
     */
    protected function setData($parameters)
    {
        $interactionCurrentData  = $this->businessOverviewReportManager->getBusinessOverviewReportData($parameters);

        $keywordsData = $this->keywordsReportManager->getKeywordsData($parameters);

        $this->activeSheet = $this->phpExcelObject->setActiveSheetIndex(0);

        $this->generateCommonHeader(
            current($interactionCurrentData['dates']),
            end($interactionCurrentData['dates'])
        );
        $this->generateBusinessInfoTable($parameters['businessProfile']);
        $this->generateKeywordsTable($keywordsData);

        $this->generateInteractionTable($interactionCurrentData);

        return $this->phpExcelObject;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    protected function generateBusinessInfoTable(BusinessProfile $businessProfile)
    {
        $this->activeSheet->setCellValue('E2', 'Name');
        $this->activeSheet->setCellValue('F2', $businessProfile->getName());
        $this->activeSheet->setCellValue('E3', 'Address');
        $this->activeSheet->setCellValue('F3', $businessProfile->getFullAddress());

        $this->setFontStyle('E', 2);
        $this->setFontStyle('E', 3);

        $this->setHeaderFontStyle('E', 2);
        $this->setHeaderFontStyle('E', 3);

        $this->setHeaderFontStyle('F', 2);
        $this->setHeaderFontStyle('F', 3);
    }

    /**
     * @param array $interactionData
     */
    protected function generateInteractionTable($interactionData)
    {
        $row = $this->interactionInitRow;
        $col = $this->interactionInitCol;

        $mapping = BusinessOverviewModel::EVENT_TYPES;

        foreach (current($interactionData['results']) as $key => $item) {
            if (!empty($mapping[$key])) {
                $label = $mapping[$key];
            } else {
                $label = $key;
            }

            $this->activeSheet->setCellValue(
                $col . $row,
                $this->translator->trans($label)
            );

            $this->setTextAlignmentStyle($col, $row);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
            $col++;
        }

        foreach ($interactionData['results'] as $rowData) {
            $col = $this->interactionInitCol;
            $row++;

            foreach ($rowData as $item) {
                $this->activeSheet->setCellValue($col . $row, $item);

                $this->setColumnSizeStyle($col);
                $this->setBorderStyle($col, $row);

                $col++;
            }

            $this->setRowSizeStyle($row);
        }

        $col = $this->interactionInitCol;
        $row++;

        $this->activeSheet->setCellValue(
            $col . $row,
            $this->translator->trans('interaction_report.total')
        );
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);

        foreach ($interactionData['total'] as $item) {
            $col++;

            $this->activeSheet->setCellValue($col . $row, $item);

            $this->setColumnSizeStyle($col);
            $this->setFontStyle($col, $row);
            $this->setBorderStyle($col, $row);
        }

        $this->setRowSizeStyle($row);
    }

    /**
     * @param array $keywordsData
     */
    protected function generateKeywordsTable($keywordsData)
    {
        $row = $this->keywordsTableInitRow;
        $col = $this->keywordsTableInitCol;

        $this->activeSheet->setCellValue($col . $row, 'Keyword');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $col++;

        $this->activeSheet->setCellValue($col . $row, 'Number of searches');
        $this->setFontStyle($col, $row);
        $this->setBorderStyle($col, $row);
        $row++;

        foreach ($keywordsData['results'] as $keyword => $searches) {
            $col = $this->keywordsTableInitCol;
            $this->activeSheet->setCellValue($col . $row, $keyword);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $col++;
            $this->activeSheet->setCellValue($col . $row, $searches);

            $this->setColumnSizeStyle($col);
            $this->setBorderStyle($col, $row);

            $row++;
        }
    }
}
