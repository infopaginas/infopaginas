<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\SubscriptionPlan;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Entity\BusinessOverviewReportBusinessProfile;
use Domain\ReportBundle\Entity\CategoryReport;
use Domain\ReportBundle\Entity\CategoryReportCategory;
use Domain\ReportBundle\Entity\SubscriptionReport;
use Domain\ReportBundle\Entity\SubscriptionReportSubscription;
use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Model\BusinessOverviewReportTypeInterface;
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

    /** @var DataFetcher $gaDataSource */
    protected $gaDataSource;

    /** @var Kernel $kernel */
    protected $kernel;

    /** @var Router $router */
    protected $router;

    /**
     * BusinessOverviewReportManager constructor.
     * @param BusinessProfileManager $businessProfileManager
     */
    public function __construct(BusinessProfileManager $businessProfileManager, DataFetcher $gaDataSource, Router $router, Kernel $kernel)
    {
        $this->businessProfileManager = $businessProfileManager;
        $this->gaDataSource = $gaDataSource;

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

    public function getBusinessOverviewDataDb(array $params = [])
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
            $dimension = 'yearMonth';
            $step = DatesUtil::STEP_MONTH;
        } else {
            $dimension = 'date';
            $step = DatesUtil::STEP_DAY;
        }

        $result['dates'] = DatesUtil::dateRange($dates, $step);
        $businessViews = $this->getBusinessProfileViews($params, $dates);

        $businessProfileResult = $this->prepareBusinessProfileOverviewReportStatsDb(
            $result['dates'],
            $businessViews['results']
        );

        $result['results'] = $businessProfileResult['results'];
        $result['views'] = $businessProfileResult['views'];
        $result['impressions'] = $businessProfileResult['impressions'];
        //dump($result);exit();
        return $result;
    }

    /**
     * @param array $params
     * @return array
     */
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
            $dimension = 'yearMonth';
            $step = DatesUtil::STEP_MONTH;
        } else {
            $dimension = 'date';
            $step = DatesUtil::STEP_DAY;
        }

        $result['dates']       = DatesUtil::dateRange($dates, $step);
        $result['views']       = $this->getBusinessProfileGaViews($businessProfile, $dates, $dimension);
        $result['impressions'] = $this->getBusinessProfileGaImpressions($businessProfile, $dates, $dimension);

        $result['results']     = $this->prepareBusinessProfileOverviewReportStats(
            $result['dates'],
            $result['views'],
            $result['impressions']
        );
dump($result);exit();
        return $result;
    }

    protected function getDateRangeVOFromDateString(string $start, string $end) : ReportDatesRangeVO
    {
        $startDate = \DateTime::createFromFormat('d-m-Y', $start);
        $endDate = \DateTime::createFromFormat('d-m-Y', $end);

        return new ReportDatesRangeVO($startDate, $endDate);
    }

    protected function prepareBusinessProfileOverviewReportStats($dates, $views, $impressions) : array
    {
        $stats = [];

        foreach ($dates as $key => $date) {
            if (!isset($views[$key]) || !isset($impressions[$key])) {
                continue;
            }

            $stats[$date] = [
                'date' => $date,
                'views' => $views[$key],
                'impressions' => $impressions[$key],
            ];
        }

        return $stats;
    }

    protected function prepareBusinessProfileOverviewReportStatsDb($dates, $views) : array
    {
        $stats = [];
        $dates = array_flip($dates);

        foreach ($dates as $date => $key) {
            $stats['results'][$date] = [
                'date' => $date,
                'views' => 0,
                'impressions' => 0,
            ];
            $stats['views'][$key] = 0;
            $stats['impressions'][$key] = 0;
        }

        foreach ($views as $view) {
            $viewDate = $view->getDate()->format('d.m.Y');
            $views = $view->getViews();
            $impressions = $view->getImpressions();

            $stats['results'][$viewDate]['views'] = $views;
            $stats['results'][$viewDate]['impressions'] = $impressions;
            $stats['views'][$dates[$viewDate]] = $views;
            $stats['impressions'][$dates[$viewDate]] = $impressions;
        }

        return $stats;
    }

    protected function getBusinessProfileGaViews(
        BusinessProfile $businessProfile,
        ReportDatesRangeVO $dates,
        string $dimension
    ) : array
    {
        $path = $this->getRouter()->generate('domain_business_profile_view', [
            'slug'     => $businessProfile->getSlug(),
            'citySlug' => $businessProfile->getCatalogLocality()->getSlug(),
        ]);

        //only for dev env - remove app_dev.php from URL (GA doesn't track it)
        if ($this->getKernel()->getEnvironment() == 'dev') {
            $path = str_replace('/app_dev.php', '', $path);
        }

        $views = $this->getGaDataSource()->getViews($path, $dates, $dimension);

        return array_map(function($value) {
            return (int)$value[1];
        }, $views);
    }

    protected function getBusinessProfileGaImpressions(
        BusinessProfile $businessProfile,
        ReportDatesRangeVO $dates,
        string $dimension
    ) : array
    {
        $impressions = $this->getGaDataSource()->getImpressions($businessProfile->getSlug(), $dates, $dimension);

        return array_map(function($value) {
            return (int)$value[1];
        }, $impressions);
    }

    protected function getBusinessProfileViews(array $params, ReportDatesRangeVO $dates) : array
    {
        $em = $this->getEntityManager();
        /** @var BusinessOverviewReport[] $businessViews */
        $businessViews = $em->getRepository('DomainReportBundle:BusinessOverviewReport')->getBusinessOverviewReportData(
            $params
        );

        return $businessViews;
    }

    public function registerBusinessViewDb(array $businessProfileId)
    {
        $this->registerBusinessOverviewDb(
            BusinessOverviewReportTypeInterface::TYPE_CODE_VIEW,
            $businessProfileId
        );
    }

    public function registerBusinessImpressionDb(array $businessProfileId)
    {
        $this->registerBusinessOverviewDb(
            BusinessOverviewReportTypeInterface::TYPE_CODE_IMPRESSION,
            $businessProfileId
        );
    }

    /**
     * @param $type
     * @param array $businessProfileIds
     */
    private function registerBusinessOverviewDb($type, array $businessProfileIds)
    {
        if (!array_key_exists($type, BusinessOverviewReportBusinessProfile::getTypes()) || !$businessProfileIds) {
            throw new \InvalidArgumentException(sprintf('Invalid Business overview report type (%s)'), $type);
        }

        $em = $this->getEntityManager();
        $datetime = new \DateTime();
        $params = [
            'dateTime' => $datetime,
            'businessProfileId' => $businessProfileIds,
        ];

        $businessOverviewReports = $em->getRepository('DomainReportBundle:BusinessOverviewReport')
            ->getBusinessOverviewReportByParams(
                $params
            );

        $reports = [];
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

            if ($type == BusinessOverviewReportTypeInterface::TYPE_CODE_IMPRESSION) {
                $businessOverviewReport->setImpressions($businessOverviewReport->getImpressions() + 1);
            } else {
                $businessOverviewReport->setViews($businessOverviewReport->getViews() + 1);
            }

            $em->persist($businessOverviewReport);
        }

        $em->flush();
    }

    /**
     * @param int $businessProfileId
     * @param \DateTime|null $datetime
     */
    public function registerBusinessView(int $businessProfileId, \DateTime $datetime = null)
    {
        $this->registerBusinessOverview(
            BusinessOverviewReportTypeInterface::TYPE_CODE_VIEW,
            $businessProfileId,
            $datetime
        );
    }

    /**
     * @param int $businessProfileId
     * @param \DateTime|null $datetime
     */
    public function registerBusinessImpression(int $businessProfileId, \DateTime $datetime = null)
    {
        $this->registerBusinessOverview(
            BusinessOverviewReportTypeInterface::TYPE_CODE_IMPRESSION,
            $businessProfileId,
            $datetime
        );
    }

    /**
     * @param $type
     * @param int $businessProfileId
     * @param \DateTime|null $datetime
     */
    private function registerBusinessOverview($type, int $businessProfileId, \DateTime $datetime = null)
    {
        if (!array_key_exists($type, BusinessOverviewReportBusinessProfile::getTypes())) {
            throw new \InvalidArgumentException(sprintf('Invalid Business overview report type (%s)'), $type);
        }

        $em = $this->getEntityManager();

        $businessProfile = $em->getRepository('DomainBusinessBundle:BusinessProfile')->findOneBy([
            'id' => $businessProfileId
        ]);

        if (!$businessProfile) {
            throw new \InvalidArgumentException(sprintf('Invalid Business profile Id (%s)'), $businessProfileId);
        }

        $datetime = ($datetime) ? $datetime : new \DateTime();

        $businessOverviewReport = $em->getRepository('DomainReportBundle:BusinessOverviewReport')->findOneBy([
            'date' => $datetime
        ]);

        if (!$businessOverviewReport) {
            $businessOverviewReport = new BusinessOverviewReport();
            $businessOverviewReport->setDate($datetime);
        }

        $businessOverviewReportBusinessProfile = new BusinessOverviewReportBusinessProfile();
        $businessOverviewReportBusinessProfile->setBusinessOverviewReport($businessOverviewReport);
        $businessOverviewReportBusinessProfile->setBusinessProfile($businessProfile);
        $businessOverviewReportBusinessProfile->setDatetime($datetime);
        $businessOverviewReportBusinessProfile->setType($type);

        $em->persist($businessOverviewReport);
        $em->persist($businessOverviewReportBusinessProfile);

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

    protected function getGaDataSource() : DataFetcher
    {
        return $this->gaDataSource;
    }
}
