<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Entity\BusinessOverviewReport;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Ivory\CKEditorBundle\Exception\Exception;
use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Domain\ReportBundle\Manager\BaseReportManager;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Router;

class BusinessOverviewReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME_RAW       = 'overview_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'overview_aggregate';

    const MONGO_DB_FIELD_ACTION      = 'action';
    const MONGO_DB_FIELD_BUSINESS_ID = 'business_id';
    const MONGO_DB_FIELD_COUNT       = 'count';
    const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

    /** @var  BusinessProfileManager $businessProfileManager */
    protected $businessProfileManager;

    /** @var Kernel $kernel */
    protected $kernel;

    /** @var Router $router */
    protected $router;

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /**
     * BusinessOverviewReportManager constructor.
     * @param BusinessProfileManager $businessProfileManager
     */
    public function __construct(
        BusinessProfileManager $businessProfileManager,
        Router $router,
        Kernel $kernel,
        MongoDbManager $mongoDbManager
    )
    {
        $this->businessProfileManager = $businessProfileManager;

        $this->kernel = $kernel;
        $this->router = $router;
        $this->mongoDbManager = $mongoDbManager;
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

    public function getBusinessOverviewReportData(array $params = [])
    {
        $businessProfile = $this->getBusinessProfileManager()->find((int)$params['businessProfileId']);

        $businessProfileName = $businessProfile->getTranslation(
            BusinessProfile::BUSINESS_PROFILE_FIELD_NAME,
            $this->getContainer()->getParameter('locale')
        );

        $result = [
            'dates' => [],
            BusinessOverviewReport::TYPE_CODE_IMPRESSION => [],
            BusinessOverviewReport::TYPE_CODE_VIEW => [],
            'results' => [],
            'datePeriod' => [
                'start' => $params['date']['start'],
                'end' => $params['date']['end'],
            ],
            'total' => [],
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

        $params['dateObject'] = $dates;

        $overviewResult = $this->getBusinessInteractionData($params);

        $businessProfileResult = $this->prepareBusinessOverviewReportStats(
            $result['dates'],
            $overviewResult,
            $dateFormat
        );

        $result['results']     = $businessProfileResult['results'];
        $result['total']        = $businessProfileResult['total'];
        $result['overall']      = $businessProfileResult['overall'];

        $viewKey = BusinessOverviewReport::TYPE_CODE_VIEW;
        $impressionKey = BusinessOverviewReport::TYPE_CODE_IMPRESSION;

        $result[$viewKey]       = $businessProfileResult[$viewKey];
        $result[$impressionKey] = $businessProfileResult[$impressionKey];

        return $result;
    }

    // see https://jira.oxagile.com/browse/INFT-913
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
        $startDate = \DateTime::createFromFormat(DatesUtil::START_END_DATE_ARRAY_FORMAT, $start);
        $endDate = \DateTime::createFromFormat(DatesUtil::START_END_DATE_ARRAY_FORMAT, $end);

        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59);

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

    protected function prepareBusinessOverviewReportStats($dates, $rawResult, $dateFormat) : array
    {
        $stats = [];
        $dates = array_flip($dates);

        foreach ($dates as $date => $key) {
            $stats['results'][$date]['date'] = $date;

            // for table and api
            foreach (BusinessOverviewReport::getTypes() as $type) {
                $stats['results'][$date][$type] = 0;
            }

            // for chart only
            $stats[BusinessOverviewReport::TYPE_CODE_VIEW][$key]       = 0;
            $stats[BusinessOverviewReport::TYPE_CODE_IMPRESSION][$key] = 0;
        }

        foreach (BusinessOverviewReport::getTypes() as $type) {
            $stats['total'][$type] = 0;
        }

        $stats['overall'] = 0;

        foreach ($rawResult as $item) {
            $action = $item[self::MONGO_DB_FIELD_ACTION];

            if (in_array($action, BusinessOverviewReport::getTypes())) {
                $count  = $item[self::MONGO_DB_FIELD_COUNT];
                $datetime = $item[self::MONGO_DB_FIELD_DATE_TIME]->toDateTime();

                $viewDate = $datetime->format($dateFormat);

                // for table and api
                $stats['results'][$viewDate][$action] += $count;

                // for chart only
                if ($action == BusinessOverviewReport::TYPE_CODE_VIEW or $action == BusinessOverviewReport::TYPE_CODE_IMPRESSION) {
                    $stats[$action][$dates[$viewDate]] += $count;
                }

                $stats['total'][$action] += $count;
                $stats['overall'] += $count;
            }
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
        $this->registerBusinessEvent(
            BusinessOverviewReport::TYPE_CODE_VIEW,
            $businessProfiles
        );
    }

    public function registerBusinessImpression(array $businessProfiles)
    {
        $this->registerBusinessEvent(
            BusinessOverviewReport::TYPE_CODE_IMPRESSION,
            $businessProfiles
        );
    }

    public function registerBusinessInteraction($businessProfileId, $type)
    {
        if ($businessProfileId and $type) {
            $businessProfile = $this->getBusinessProfileManager()->getRepository()->find($businessProfileId);

            if ($businessProfile) {
                $result = $this->registerBusinessEvent(
                    $type,
                    [$businessProfile]
                );

                return $result;
            }
        }

        return false;
    }

    /**
     * @param $type
     * @param array $businessProfiles
     *
     * @return bool
     */
    private function registerBusinessEvent($type, array $businessProfiles)
    {
        if (!in_array($type, BusinessOverviewReport::getTypes()) or !$businessProfiles) {
            return false;
        }

        $businessProfileIds = BusinessProfileUtil::extractBusinessProfiles($businessProfiles);

        foreach ($businessProfileIds as $businessProfileId) {
            $this->insertBusinessInteraction($businessProfileId, $type);
        }

        $this->insertBusinessInteraction(0, $type);

        return true;
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

    protected function insertBusinessInteraction($businessProfileId, $action)
    {
        $this->mongoDbManager->insertOne(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            [
                self::MONGO_DB_FIELD_BUSINESS_ID => $businessProfileId,
                self::MONGO_DB_FIELD_ACTION      => $action,
                self::MONGO_DB_FIELD_DATE_TIME   => $this->mongoDbManager->typeUTCDateTime(new \DateTime()),
            ]
        );
    }

    protected function getBusinessInteractionData($params)
    {
        $cursor = $this->mongoDbManager->find(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
                self::MONGO_DB_FIELD_BUSINESS_ID => (int)$params['businessProfileId'],
                self::MONGO_DB_FIELD_DATE_TIME => [
                    '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
                    '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
                ],
            ]
        );

        return $cursor;
    }

    public function aggregateBusinessInteractions($period)
    {
        $aggregateStartDate = $this->mongoDbManager->typeUTCDateTime($period->getStartDate());
        $aggregateEndDate   = $this->mongoDbManager->typeUTCDateTime($period->getEndDate());

        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            [
                [
                    '$match' => [
                        self::MONGO_DB_FIELD_DATE_TIME => [
                            '$gte' => $aggregateStartDate,
                            '$lte' => $aggregateEndDate,
                        ],
                    ],
                ],
                [
                    '$project' => [
                        'query' => [
                            'action' => '$' . self::MONGO_DB_FIELD_ACTION,
                            'bid'    => '$' . self::MONGO_DB_FIELD_BUSINESS_ID,
                        ],
                    ]
                ],
                [
                    '$group' => [
                        '_id' => '$query',
                        self::MONGO_DB_FIELD_COUNT => [
                            '$sum' => 1,
                        ],
                    ],
                ]
            ]
        );

        foreach ($cursor as $document) {
            $document[self::MONGO_DB_FIELD_ACTION]      = $document['_id']['action'];
            $document[self::MONGO_DB_FIELD_BUSINESS_ID] = $document['_id']['bid'];
            $document[self::MONGO_DB_FIELD_DATE_TIME]   = $aggregateStartDate;

            $document['_id'] = $this->mongoDbManager->generateId();

            $this->mongoDbManager->insertOne(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, $document);
        }
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
