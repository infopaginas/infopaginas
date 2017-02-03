<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Ivory\CKEditorBundle\Exception\Exception;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Domain\ReportBundle\Manager\BaseReportManager;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Router;

class BusinessOverviewReportManager extends BaseReportManager
{
    /** @var  BusinessProfileManager $businessProfileManager */
    protected $businessProfileManager;

    /** @var Kernel $kernel */
    protected $kernel;

    /** @var Router $router */
    protected $router;

    /**
     * BusinessOverviewReportManager constructor.
     * @param BusinessProfileManager $businessProfileManager
     */
    public function __construct(BusinessProfileManager $businessProfileManager, Router $router, Kernel $kernel)
    {
        $this->businessProfileManager = $businessProfileManager;

        $this->kernel = $kernel;
        $this->router = $router;
    }

    /**
     * @param array $filterParams
     * @return array
     */
    public function getBusinessOverviewDataByFilterParams(array $filterParams = [])
    {
        $params = [];

        if (isset($filterParams['_page'])) {
            $params['page'] = $filterParams['_page'];
        }

        if (isset($filterParams['_per_page'])) {
            $params['perPage'] = $filterParams['_per_page'];
        }

        if (isset($filterParams['date'])) {
            $params['date'] = $filterParams['date']['value'];
        }

        if (isset($filterParams['periodOption'])) {
            $params['periodOption'] = $filterParams['periodOption']['value'];
        }

        $businessKey = 'businessOverviewReportBusinessProfiles__businessProfile';

        if (isset($filterParams[$businessKey]) &&
            $filterParams[$businessKey]['value'] != ''
        ) {
            $params['businessProfileId'] = $filterParams[$businessKey]['value'];
        } else {
            $params['businessProfileId'] = $this->businessProfileManager->findOneBusinessProfile()->getId();
        }

        return $this->getBusinessOverviewData($params);
    }

    public function getBusinessOverviewData(array $params = [])
    {
        $businessProfile = $this->getBusinessProfileManager()->find((int)$params['businessProfileId']);

        $businessProfileName = $businessProfile->getTranslation(
            'name',
            $this->getContainer()->getParameter('locale')
        );

        $result = [
            'dates' => [],
            'impressions' => [],
            'views' => [],
            'results' => [],
            'datePeriod' => [
                'start' => $params['date']['start'],
                'end' => $params['date']['end'],
            ],
            'businessProfile' => $businessProfileName
        ];

        $dates = $this->getDateRangeVOFromDateString(
            $params['date']['start'],
            $params['date']['end']
        );

        if (isset($params['periodOption']) && $params['periodOption'] == AdminHelper::PERIOD_OPTION_CODE_PER_MONTH) {
            $dateFormat = AdminHelper::DATE_MONTH_FORMAT;
            $step       = DatesUtil::STEP_MONTH;
        } else {
            $dateFormat = AdminHelper::DATE_FORMAT;
            $step       = DatesUtil::STEP_DAY;
        }

        $result['dates'] = DatesUtil::dateRange($dates, $step, $dateFormat);

        $businessViews = $this->getBusinessProfileViews($params);

        $businessProfileResult = $this->prepareBusinessProfileOverviewReportStats(
            $result['dates'],
            $businessViews['results'],
            $dateFormat
        );

        $result['results'] = $businessProfileResult['results'];
        $result['views'] = $businessProfileResult['views'];
        $result['impressions'] = $businessProfileResult['impressions'];

        return $result;
    }

    protected function getDateRangeVOFromDateString(string $start, string $end) : ReportDatesRangeVO
    {
        $startDate = \DateTime::createFromFormat('d-m-Y', $start);
        $endDate = \DateTime::createFromFormat('d-m-Y', $end);

        return new ReportDatesRangeVO($startDate, $endDate);
    }

    protected function prepareBusinessProfileOverviewReportStats($dates, $views, $dateFormat) : array
    {
        $stats = [];
        $dates = array_flip($dates);

        foreach ($dates as $date => $key) {
            $stats['results'][$date] = [
                'date' => $date,
                'dateObject' => \DateTime::createFromFormat($dateFormat, $date),
                'views' => 0,
                'impressions' => 0,
            ];
            $stats['views'][$key] = 0;
            $stats['impressions'][$key] = 0;
        }

        foreach ($views as $view) {
            $viewDate    = $view->getDate()->format($dateFormat);
            $views       = $view->getViews();
            $impressions = $view->getImpressions();

            $stats['results'][$viewDate]['views']       += $views;
            $stats['results'][$viewDate]['impressions'] += $impressions;
            $stats['views'][$dates[$viewDate]]          += $views;
            $stats['impressions'][$dates[$viewDate]]    += $impressions;
        }

        return $stats;
    }

    protected function getBusinessProfileViews(array $params) : array
    {
        $em = $this->getEntityManager();
        /** @var BusinessOverviewReport[] $businessViews */
        $businessViews = $em->getRepository('DomainReportBundle:BusinessOverviewReport')->getBusinessOverviewReportData(
            $params
        );

        return $businessViews;
    }

    public function registerBusinessView(array $businessProfiles)
    {
        $this->registerBusinessOverview(
            BusinessOverviewReport::TYPE_CODE_VIEW,
            $businessProfiles
        );
    }

    public function registerBusinessImpression(array $businessProfiles)
    {
        $this->registerBusinessOverview(
            BusinessOverviewReport::TYPE_CODE_IMPRESSION,
            $businessProfiles
        );
    }

    /**
     * @param $type
     * @param array $businessProfiles
     *
     * @return bool
     */
    private function registerBusinessOverview($type, array $businessProfiles)
    {
        if (!in_array($type, BusinessOverviewReport::getTypes())) {
            throw new \InvalidArgumentException(sprintf('Invalid Business overview report type (%s)'), $type);
        }

        if (!$businessProfiles) {
            return false;
        }

        $em = $this->getEntityManager();

        $datetime           = new \DateTime();
        $businessProfileIds = BusinessProfileUtil::extractBusinessProfiles($businessProfiles);

        $reports  = [];
        $params   = [
            'dateTime'          => $datetime,
            'businessProfileId' => $businessProfileIds,
        ];

        $businessOverviewReports = $em->getRepository('DomainReportBundle:BusinessOverviewReport')
            ->getBusinessOverviewReportByParams($params);

        foreach ($businessOverviewReports as $businessOverviewReport) {
            $reports[$businessOverviewReport->getBusinessProfile()->getId()] = $businessOverviewReport;
        }

        foreach ($businessProfileIds as $businessProfileId) {
            if (empty($reports[$businessProfileId])) {
                $businessOverviewReport = new BusinessOverviewReport();
                $businessOverviewReport->setDate($datetime);
                $businessOverviewReport->setBusinessProfile(
                    $this->getEntityManager()->getReference('DomainBusinessBundle:BusinessProfile', $businessProfileId)
                );
            } else {
                $businessOverviewReport = $reports[$businessProfileId];
            }

            $businessOverviewReport->incrementBusinessCounter($type);
            /*if ($type == BusinessOverviewReport::TYPE_CODE_IMPRESSION) {
                $businessOverviewReport->incrementImpressions();
            } else {
                $businessOverviewReport->incrementViews();
            }*/

            $em->persist($businessOverviewReport);
        }

        $em->flush();
    }

    /**
     * @param array $filterParams
     * @param string $format
     * @return mixed
     */
    public function getBusinessOverviewReportDataAndName(array $filterParams, string $format) : array
    {
        $businessOverviewData = $this->getBusinessOverviewDataByFilterParams($filterParams);

        if ($businessOverviewData['businessProfile']) {
            $reportName = str_replace(' ', '_', $businessOverviewData['businessProfile']);
        } else {
            $reportName = 'business_overview_report';
        }

        $filename = $this->generateReportName($format, $reportName);

        return [$businessOverviewData, $filename];
    }

    protected function getKernel()
    {
        return $this->kernel;
    }

    protected function getRouter()
    {
        return $this->router;
    }

    protected function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
    }
}
