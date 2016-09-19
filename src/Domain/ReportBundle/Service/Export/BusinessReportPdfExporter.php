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
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Spraed\PDFGeneratorBundle\PDFGenerator\PDFGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class BusinessReportPdfExporter
{
    protected $entityManager;
    protected $businessOverviewReportManager;
    protected $keywordsReportManager;
    protected $adUsageReportManager;
    protected $templateEngine;
    protected $pdfGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        BusinessOverviewReportManager $businessOverviewReportManager,
        KeywordsReportManager $keywordsReportManager,
        AdUsageReportManager $adUsageReportManager,
        EngineInterface $templateEngine,
        PDFGenerator $pdfGenerator
    ) {
        $this->entityManager = $entityManager;

        $this->businessOverviewReportManager = $businessOverviewReportManager;

        $this->keywordsReportManager = $keywordsReportManager;

        $this->adUsageReportManager = $adUsageReportManager;

        $this->templateEngine = $templateEngine;

        $this->pdfGenerator = $pdfGenerator;
    }

    public function export(array $params)
    {
        $businessProfile = $this->getBusinessProfilesRepo()->find($params['businessProfileId']);

        $overviewData = $this->getBusinessOverviewReportManager()->getBusinessOverviewData($params);
        $keywordsData = $this->getKeywordsReportManager()->getKeywordsData($params);

        $thisMonthOverviewData = $this->getOverviewDataBySpecifiedDatePeriod(
            $businessProfile->getId(), DatesUtil::RANGE_THIS_MONTH
        );

        $lastMonthOverviewData = $this->getOverviewDataBySpecifiedDatePeriod(
            $businessProfile->getId(),
            DatesUtil::RANGE_LAST_MONTH
        );

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

        $adUsageData = $this->getAdUsageReportManager()->getAdUsageData($params);

        $filename = str_replace(' ', '', $businessProfile->getName()) . '_' . (new \DateTime('now'))->format('dmY_H:i:s') . '.pdf';

        $html = $this->templateEngine->render(
            'DomainReportBundle:PDF:template.html.twig',
            array(
                'businessProfile' => $businessProfile,
                'overviewData' => $overviewData,
                'keywordsData' => $keywordsData,
                'thisMonthOverviewData' => $thisMonthOverviewData,
                'lastMonthOverviewData' => $lastMonthOverviewData,
                'thisYearOverviewData' => $thisYearOverviewData,
                'lastYearOverviewData' => $lastYearOverviewData,
                'adUsageData' => $adUsageData,
                'data' => []
            )
        );

        $content = $this->pdfGenerator->generatePDF($html, 'UTF-8');

        return [$filename, $content];
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