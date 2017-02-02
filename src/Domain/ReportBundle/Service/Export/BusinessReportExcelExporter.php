<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 18.09.16
 * Time: 16:36
 */

namespace Domain\ReportBundle\Service\Export;


use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Manager\AdUsageReportManager;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Domain\ReportBundle\Manager\KeywordsReportManager;
use Domain\ReportBundle\Util\DatesUtil;
use Liuggio\ExcelBundle\Factory;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BusinessReportExcelExporter
{
    protected $entityManager;
    protected $businessOverviewReportManager;
    protected $keywordsReportManager;
    protected $adUsageReportManager;
    protected $excel;

    public function __construct(
        EntityManagerInterface $entityManager,
        BusinessOverviewReportManager $businessOverviewReportManager,
        KeywordsReportManager $keywordsReportManager,
        AdUsageReportManager $adUsageReportManager,
        Factory $excel
    ) {
        $this->entityManager = $entityManager;

        $this->businessOverviewReportManager = $businessOverviewReportManager;

        $this->keywordsReportManager = $keywordsReportManager;

        $this->adUsageReportManager = $adUsageReportManager;

        $this->excel = $excel;
    }

    public function export(array $params)
    {
        $businessProfile = $this->getBusinessProfilesRepo()->find($params['businessProfileId']);

        $excel = $this->getExcelService();

        $phpExcelObject = $excel->createPHPExcelObject();

        $phpExcelObject->getProperties()
            ->setTitle($businessProfile->getName())
        ;

        $phpExcelObject->getActiveSheet()
            ->setTitle(mb_substr($businessProfile->getName(), 0, 31));

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $activeSheet = $phpExcelObject->setActiveSheetIndex(0);

        $activeSheet->setCellValue('A1', $businessProfile->getName());

        $this->makeCellValueBold($activeSheet, 'A1');

        $activeSheet->setCellValue('A2', $businessProfile->getFullAddress());

        $activeSheet->setCellValue('A4', $params['date']['start'] . ' - ' . $params['date']['end']);
        $this->makeCellValueBold($activeSheet, 'A4');

        $overviewData = $this->getBusinessOverviewReportManager()->getBusinessOverviewData($params);

        $thisMonthOverviewData = $this->getOverviewDataBySpecifiedDatePeriod(
            $businessProfile->getId(), DatesUtil::RANGE_THIS_MONTH
        );

        $lastMonthOverviewData = $this->getOverviewDataBySpecifiedDatePeriod(
            $businessProfile->getId(),
            DatesUtil::RANGE_LAST_MONTH
        );

        $activeSheet->setCellValue('A6', 'Total Profile Impressions');
        $activeSheet->setCellValue('D6', array_sum($overviewData['impressions']));
        $activeSheet->setCellValue('A7', 'Total Profile Views');
        $activeSheet->setCellValue('D7', array_sum($overviewData['views']));

        $activeSheet->setCellValue('A9', 'Overview');

        $activeSheet->setCellValue('A10', 'Date');
        $this->makeCellValueBold($activeSheet, 'A10');

        $activeSheet->setCellValue('B10', 'Impressions');
        $this->makeCellValueBold($activeSheet, 'B10');

        $activeSheet->setCellValue('C10', 'Views');
        $this->makeCellValueBold($activeSheet, 'C10');

        $activeSheet->setCellValue('D9', 'Keyword');

        $activeSheet->setCellValue('D10', 'Keyword');
        $this->makeCellValueBold($activeSheet, 'D10');

        $activeSheet->setCellValue('E10', 'Number of searches');
        $this->makeCellValueBold($activeSheet, 'E10');

        $overviewCellIndex = 11;

        foreach ($overviewData['results'] as $overview) {
            $activeSheet->setCellValue('A' . $overviewCellIndex, $overview['date']);
            $activeSheet->setCellValue('B' . $overviewCellIndex, $overview['impressions']);
            $activeSheet->setCellValue('C' . $overviewCellIndex, $overview['views']);
            $overviewCellIndex++;
        }

        $keywordsData = $this->getKeywordsReportManager()->getKeywordsData($params);

        $keywordCellIndex = 11;

        foreach ($keywordsData['results'] as $keyword => $searches) {
            $activeSheet->setCellValue('D' . $keywordCellIndex, $keyword);
            $activeSheet->setCellValue('E' . $keywordCellIndex, $searches);
            $keywordCellIndex++;
        }

        $adUsageData = $this->getAdUsageReportManager()->getAdUsageData($params);

        $adUsageColumnIndex = \PHPExcel_Cell::columnIndexFromString('E');

        foreach ($adUsageData as $bannerName => $stats) {
            $dateColumn = \PHPExcel_Cell::stringFromColumnIndex($adUsageColumnIndex);
            $impressionsColumn = \PHPExcel_Cell::stringFromColumnIndex(($adUsageColumnIndex + 1));
            $clicksColumn = \PHPExcel_Cell::stringFromColumnIndex(($adUsageColumnIndex + 2));

            $columnLetter = \PHPExcel_Cell::stringFromColumnIndex($adUsageColumnIndex);
            $activeSheet->setCellValue($columnLetter . '9', $bannerName);

            $activeSheet->setCellValue($columnLetter . '10', 'Date');
            $this->makeCellValueBold($activeSheet, $columnLetter . '10');

            $activeSheet->setCellValue($impressionsColumn . '10', 'Impressions');
            $this->makeCellValueBold($activeSheet, $impressionsColumn . '10');

            $activeSheet->setCellValue($clicksColumn . '10', 'Clicks');
            $this->makeCellValueBold($activeSheet, $clicksColumn . '10');

            $adUsageCellIndex = 11;

            foreach ($stats as $date => $adUsageData) {
                $date = \DateTime::createFromFormat('j/n/y', $date)->format('d.m.Y');
                $activeSheet->setCellValue($dateColumn . $adUsageCellIndex, $date);
                $activeSheet->setCellValue($impressionsColumn . $adUsageCellIndex, $adUsageData['impressions']);
                $activeSheet->setCellValue($clicksColumn . $adUsageCellIndex, $adUsageData['clicks']);
                $adUsageCellIndex++;
            }

            $adUsageColumnIndex += 3;
        }

        $currentColumnIndex = $adUsageColumnIndex;

        $activeSheet->setCellValue(
            \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex) . '9',
            'Monthly Impressions Dynamics'
        );
        $activeSheet->setCellValue(
            \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex) . '10',
            'Date'
        );
        $this->makeCellValueBold($activeSheet, \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex) . '10');

        $activeSheet->setCellValue(
            \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 1) . '10',
            (new \DateTime('now'))->modify('-1 month')->format('F, Y')
        );
        $this->makeCellValueBold($activeSheet, \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 1) . '10');

        $activeSheet->setCellValue(
            \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 2) . '10',
            (new \DateTime('now'))->format('F, Y')
        );
        $this->makeCellValueBold($activeSheet, \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 2) . '10');


        $thisMonthOverviewCellIndex = 11;
        $counter = 1;
        $monthMaxIndex = max(count($thisMonthOverviewData), count($lastMonthOverviewData));

        for ($index = 0; $index < $monthMaxIndex; $index++) {
            $activeSheet->setCellValue(
                \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex) . $thisMonthOverviewCellIndex,
                $counter
            );

            $activeSheet->setCellValue(
                \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 1) . $thisMonthOverviewCellIndex,
                isset($lastMonthOverviewData[$index]) ? $lastMonthOverviewData[$index]['impressions'] : '-'
            );

            $activeSheet->setCellValue(
                \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 2) . $thisMonthOverviewCellIndex,
                isset($thisMonthOverviewData[$index]) ? $thisMonthOverviewData[$index]['impressions'] : '-'
            );

            $counter++;
            $thisMonthOverviewCellIndex++;
        }

        $currentColumnIndex += 3;

        $activeSheet->setCellValue(
            \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex) . '9',
            'Yearly Impressions Dynamics'
        );
        $activeSheet->setCellValue(
            \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex) . '10',
            'Last year'
        );
        $this->makeCellValueBold($activeSheet, \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex) . '10');

        $activeSheet->setCellValue(
            \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 2) . '10',
            'Year before last year'
        );
        $this->makeCellValueBold($activeSheet, \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 2) . '10');

        $thisYearOverviewData = $this->getOverviewDataBySpecifiedDatePeriod(
            $businessProfile->getId(),
            DatesUtil::RANGE_THIS_YEAR,
            AdminHelper::PERIOD_OPTION_CODE_PER_MONTH
        );

        $lastYearOverviewData = $this->getOverviewDataBySpecifiedDatePeriod(
            $businessProfile->getId(),
            DatesUtil::RANGE_LAST_YEAR,
            AdminHelper::PERIOD_OPTION_CODE_PER_MONTH
        );

        $thisYearOverviewCellIndex = 11;

        foreach ($thisYearOverviewData as $index => $stats) {
            $activeSheet->setCellValue(
                \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex) . $thisYearOverviewCellIndex,
                $stats['dateObject']->format('F, Y')
            );

            $activeSheet->setCellValue(
                \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 1) . $thisYearOverviewCellIndex,
                $stats['impressions']
            );

            $activeSheet->setCellValue(
                \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 2) . $thisYearOverviewCellIndex,
                $lastYearOverviewData[$index]['dateObject']->format('F, Y')
            );

            $activeSheet->setCellValue(
                \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 3) . $thisYearOverviewCellIndex,
                isset($lastYearOverviewData[$index]) ? $lastYearOverviewData[$index]['impressions'] : '-'
            );

            $thisYearOverviewCellIndex++;
        }

        $activeSheet->getStyle('A9:' . \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 3) . '9')
            ->getFill()
            ->applyFromArray(array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => '3399FF'
                )
            )
        );

        $activeSheet->getStyle('A10:' . \PHPExcel_Cell::stringFromColumnIndex($currentColumnIndex + 3) . '10')
            ->getFill()
            ->applyFromArray(array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'rgb' => 'cccccc'
                    )
                )
            );

        // create the writer
        $writer = $excel->createWriter($phpExcelObject, 'Excel5');

        // create the response
        $response = $excel->createStreamedResponse($writer);

        $fileName = str_replace(' ', '', $businessProfile->getName()) . '_' . (new \DateTime('now'))->format('dmY_H:i:s') . '.xls';

        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    protected function makeCellValueBold(&$activeSheet, $cellPosition)
    {
        $activeSheet->getCell($cellPosition)->getStyle()->applyFromArray([
                'font'  => [
                    'bold'  => true,
                ],
                'backgroundColor' => '#000'
            ]
        );
    }

    protected function getOverviewDataBySpecifiedDatePeriod($businessProfileId, $range, $period = '')
    {
        $range = DatesUtil::getDateRangeValueObjectFromRangeType($range);
        $params = [
            'businessProfileId' => $businessProfileId,
            'date' => [
                'start' => $range->getStartDate()->format(DatesUtil::START_END_DATE_ARRAY_FORMAT),
                'end' => $range->getEndDate()->format(DatesUtil::START_END_DATE_ARRAY_FORMAT),
            ],
        ];

        if (!empty($period)) {
            $params['periodOption'] = $period;
        }

        $overviewData = $this->getBusinessOverviewReportManager()->getBusinessOverviewData($params);
        return array_values($overviewData['results']);
    }

    protected function getExcelService()
    {
        return $this->excel;
    }

    protected function getAdUsageReportManager() : AdUsageReportManager
    {
        return $this->adUsageReportManager;
    }

    protected function getKeywordsReportManager() : KeywordsReportManager
    {
        return $this->keywordsReportManager;
    }

    protected function getBusinessOverviewReportManager()
    {
        return $this->businessOverviewReportManager;
    }

    protected function getBusinessProfilesRepo()
    {
        return $this->entityManager->getRepository(BusinessProfile::class);
    }
}