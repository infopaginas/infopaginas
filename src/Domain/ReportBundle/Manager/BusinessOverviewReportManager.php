<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\CategoryOverviewModel;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;

class BusinessOverviewReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME_RAW       = 'overview_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'overview_aggregate';

    const MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW       = 'overview_archive_raw';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE = 'overview_archive_aggregate';

    const MONGO_DB_FIELD_ACTION      = 'action';
    const MONGO_DB_FIELD_BUSINESS_ID = 'business_id';
    const MONGO_DB_FIELD_COUNT       = 'count';
    const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

    protected $reportName = 'business_overview_report';

    /** @var  BusinessProfileManager $businessProfileManager */
    protected $businessProfileManager;

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /**
     * BusinessOverviewReportManager constructor.
     * @param BusinessProfileManager $businessProfileManager
     */
    public function __construct(BusinessProfileManager $businessProfileManager, MongoDbManager $mongoDbManager)
    {
        $this->businessProfileManager = $businessProfileManager;
        $this->mongoDbManager         = $mongoDbManager;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getBusinessOverviewReportData(array $params = [])
    {
        $businessProfile = $this->getBusinessProfileManager()->find((int)$params['businessProfileId']);

        $businessProfileName = $businessProfile->getName();

        if (!empty($params['chartType']) and in_array($params['chartType'], BusinessOverviewModel::getTypes())) {
            $chartType = $params['chartType'];
        } else {
            $chartType = '';
        }

        $result = [
            'dates' => [],
            'chart' => [],
            'chartTitle' => '',
            'chartHint'  => '',
            'results' => [],
            'datePeriod' => [
                'start' => $params['date']['start'],
                'end' => $params['date']['end'],
            ],
            'total' => [],
            'businessProfile' => $businessProfileName
        ];

        $dates = DatesUtil::getDateRangeVOFromDateString($params['date']['start'], $params['date']['end']);

        $periodOption = !empty($params['periodOption']) ? $params['periodOption'] : '';

        list($dateFormat, $step) = $this->handlePeriodOption($periodOption);

        $result['dates'] = DatesUtil::dateRange($dates, $step, $dateFormat);

        $params['dateObject'] = $dates;

        $overviewResult = $this->getBusinessInteractionData($params);

        $businessProfileResult = $this->prepareBusinessOverviewReportStats(
            $result['dates'],
            $overviewResult,
            $dateFormat,
            $chartType
        );

        $result['results'] = $businessProfileResult['results'];
        $result['total']   = $businessProfileResult['total'];
        $result['overall'] = $businessProfileResult['overall'];

        if ($chartType) {
            $result['chart']      = $businessProfileResult[$chartType];
            $result['chartTitle'] = BusinessOverviewModel::EVENT_TYPES[$chartType];
            $result['chartHint']  = BusinessOverviewModel::getChartHintByType($chartType);
        }

        return $result;
    }

    /**
     * @param array     $dates
     * @param mixed     $rawResult
     * @param string    $dateFormat
     * @param string    $chartType
     *
     * @return array
     */
    protected function prepareBusinessOverviewReportStats($dates, $rawResult, $dateFormat, $chartType = '') : array
    {
        $stats = [];
        $dates = array_flip($dates);

        foreach ($dates as $date => $key) {
            $stats['results'][$date]['date'] = $date;

            // for table and api
            foreach (BusinessOverviewModel::getTypes() as $type) {
                $stats['results'][$date][$type] = 0;
            }

            // for chart only
            if ($chartType) {
                $stats[$chartType][$key] = 0;
            }
        }

        foreach (BusinessOverviewModel::getTypes() as $type) {
            $stats['total'][$type] = 0;
        }

        $stats['overall'] = 0;

        foreach ($rawResult as $item) {
            $action = $item[self::MONGO_DB_FIELD_ACTION];

            if (in_array($action, BusinessOverviewModel::getTypes())) {
                $count  = $item[self::MONGO_DB_FIELD_COUNT];
                $datetime = DatesUtil::convertMongoDbTimeToDatetime($item[self::MONGO_DB_FIELD_DATE_TIME]);

                if ($dateFormat == AdminHelper::DATE_WEEK_FORMAT) {
                    $viewDate = DatesUtil::getWeeklyFormatterDate($datetime);
                } else {
                    $viewDate = $datetime->format($dateFormat);
                }

                // for table and api
                $stats['results'][$viewDate][$action] += $count;

                // for chart only
                if ($chartType and $action == $chartType) {
                    $stats[$action][$dates[$viewDate]] += $count;
                }

                $stats['total'][$action] += $count;
                $stats['overall'] += $count;
            }
        }

        return $stats;
    }

    /**
     * @param int $businessProfileId
     * @param string $type
     *
     * @return bool
     */
    public function registerBusinessInteraction($businessProfileId, $type)
    {
        if ($businessProfileId and $type) {
            /** @var BusinessProfile $businessProfile */
            $businessProfile = $this->getBusinessProfileManager()->getRepository()->find($businessProfileId);

            if ($businessProfile) {
                $result = $this->registerBusinessInteractionEvent(
                    $type,
                    [$businessProfile]
                );

                if (in_array($type, CategoryOverviewModel::getTypes())) {
                    $categoryOverviewReportManager = $this
                        ->getContainer()
                        ->get('domain_report.manager.category_overview_report_manager');

                    $businessProfileCategoriesIds = BusinessProfileUtil::extractEntitiesId(
                        $businessProfile->getCategories()->toArray()
                    );

                    $categoriesResult = $categoryOverviewReportManager->registerCategoriesInteractionEvent(
                        $type,
                        $businessProfileCategoriesIds
                    );

                    if(!$categoriesResult){
                        $result = false;
                    }
                }

                return $result;
            }
        }

        return false;
    }

    /**
     * @param string $type
     * @param array  $businessProfileIds
     *
     * @return bool
     */
    public function registerBusinessEvent($type, array $businessProfileIds)
    {
        if (!in_array($type, BusinessOverviewModel::getBusinessEventTypes()) or !$businessProfileIds) {
            return false;
        }

        $data = $this->buildBusinessInteractions($businessProfileIds, $type);

        $this->insertBusinessInteractions($data);

        return true;
    }

    /**
     * @param string $type
     * @param array $businessProfiles
     *
     * @return bool
     */
    private function registerBusinessInteractionEvent($type, array $businessProfiles)
    {
        if (!in_array($type, BusinessOverviewModel::getTypes()) or !$businessProfiles) {
            return false;
        }

        $businessProfileIds = BusinessProfileUtil::extractEntitiesId($businessProfiles);

        $data = $this->buildBusinessInteractions($businessProfileIds, $type);

        $this->insertBusinessInteractions($data);

        return true;
    }

    /**
     * @param string $businessSlug
     * @param string $format
     * @return string
     */
    public function getBusinessOverviewReportName($businessSlug, $format)
    {
        $this->reportName = $businessSlug;

        $filename = $this->generateReportName($format);

        return $filename;
    }

    /**
     * @param int       $businessId
     * @param string    $action
     * @param MongoDB\BSON\UTCDateTime $date
     *
     * @return array
     */
    protected function buildSingleBusinessInteraction($businessId, $action, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_BUSINESS_ID => (int) $businessId,
            self::MONGO_DB_FIELD_ACTION      => $action,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
        ];

        return $data;
    }

    /**
     * @param array $businessProfileIds
     * @param string $action
     *
     * @return array
     */
    protected function buildBusinessInteractions($businessProfileIds, $action)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        foreach ($businessProfileIds as $businessProfileId) {
            $data[] = $this->buildSingleBusinessInteraction($businessProfileId, $action, $date);
        }

        $data[] = $this->buildSingleBusinessInteraction(0, $action, $date);

        return $data;
    }

    /**
     * @param array $data
     */
    protected function insertBusinessInteractions($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            $data
        );
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function getBusinessInteractionData($params)
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

    /**
     * @param ReportDatesRangeVO $period
     */
    public function aggregateBusinessInteractions($period)
    {
        $this->mongoDbManager->createIndex(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, [
            self::MONGO_DB_FIELD_BUSINESS_ID => MongoDbManager::INDEX_TYPE_ASC,
            self::MONGO_DB_FIELD_DATE_TIME   => MongoDbManager::INDEX_TYPE_DESC,
        ]);

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

        $i = 0;
        $insert = [];

        foreach ($cursor as $document) {
            $document[self::MONGO_DB_FIELD_ACTION]      = $document['_id']['action'];
            $document[self::MONGO_DB_FIELD_BUSINESS_ID] = $document['_id']['bid'];
            $document[self::MONGO_DB_FIELD_COUNT]       = (int) $document[self::MONGO_DB_FIELD_COUNT];
            $document[self::MONGO_DB_FIELD_DATE_TIME]   = $aggregateStartDate;

            $document['_id'] = $this->mongoDbManager->generateId();

            $insert[] = $document;

            if (($i % MongoDbManager::DEFAULT_BATCH_SIZE) === 0) {
                $this->mongoDbManager->insertMany(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, $insert);
                $insert = [];
            }

            $i++;
        }

        if ($insert) {
            $this->mongoDbManager->insertMany(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, $insert);
        }
    }

    /**
     * @param \Datetime $date
     */
    public function archiveRawBusinessInteractions($date)
    {
        $this->mongoDbManager->archiveCollection(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            self::MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW,
            self::MONGO_DB_FIELD_DATE_TIME,
            $date
        );
    }

    /**
     * @param \Datetime $date
     */
    public function archiveAggregatedBusinessInteractions($date)
    {
        $this->mongoDbManager->archiveCollection(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            self::MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE,
            self::MONGO_DB_FIELD_DATE_TIME,
            $date
        );
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getThisYearSearchParams($params)
    {
        return $this->getYearSearchParam($params, DatesUtil::RANGE_THIS_YEAR);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getThisLastSearchParams($params)
    {
        return $this->getYearSearchParam($params, DatesUtil::RANGE_LAST_YEAR);
    }

    /**
     * @param array     $params
     * @param string    $range
     *
     * @return array
     */
    protected function getYearSearchParam($params, $range)
    {
        $dates = DatesUtil::getDateRangeValueObjectFromRangeType($range);

        $params['date']['start'] = $dates->getStartDate()->format(DatesUtil::START_END_DATE_ARRAY_FORMAT);
        $params['date']['end']   = $dates->getEndDate()->format(DatesUtil::START_END_DATE_ARRAY_FORMAT);

        $params['periodOption'] = AdminHelper::PERIOD_OPTION_CODE_PER_MONTH;

        return $params;
    }

    /**
     * @return BusinessProfileManager
     */
    protected function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
    }

    /**
     * @param array $businessIds
     * @param array $actions
     * @param \DateTime $startDate
     * @param \DateTime|null $endDate
     * @return mixed
     */
    protected function getSummaryByAction($businessIds, $actions, \DateTime $startDate, \DateTime $endDate = null)
    {
        $dateFilterMongo = [
            '$gte' => $this->mongoDbManager->typeUTCDateTime($startDate),
        ];

        if ($endDate) {
            $dateFilterMongo['$lte'] = $this->mongoDbManager->typeUTCDateTime($endDate);
        }

        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
                [
                    '$match' => [
                        self::MONGO_DB_FIELD_ACTION      => [
                            '$in' => $actions,
                        ],
                        self::MONGO_DB_FIELD_BUSINESS_ID => [
                            '$in' => $businessIds,
                        ],
                        self::MONGO_DB_FIELD_DATE_TIME => $dateFilterMongo,
                    ],
                ],
                [
                    '$group' => [
                        '_id' => [
                            '_id' => '$' . self::MONGO_DB_FIELD_BUSINESS_ID,
                            self::MONGO_DB_FIELD_ACTION => '$' . self::MONGO_DB_FIELD_ACTION,
                        ],
                        self::MONGO_DB_FIELD_COUNT => [
                            '$sum' => '$' . self::MONGO_DB_FIELD_COUNT,
                        ],
                    ],
                ],
            ]
        );

        return $cursor;
    }

    /**
     * @param array $businessIds
     * @param array $actions
     * @param \DateTime $startDate
     * @param \DateTime|null $endDate
     * @return array
     */
    public function getSummaryByActionData($businessIds, $actions, \DateTime $startDate, \DateTime $endDate = null)
    {
        $data = [];

        if (!$businessIds || !$actions) {
            return $data;
        }

        $cursor = $this->getSummaryByAction($businessIds, $actions, $startDate, $endDate);

        if ($cursor) {
            foreach ($cursor as $item) {
                $businessId = $item['_id']['_id'];
                $action = $item['_id']['action'];

                if (isset($data[$businessId][$action])) {
                    $data[$businessId][$action] += $item['count'];
                } else {
                    $data[$businessId][$action] = $item['count'];
                }
            }
        }

        return $data;
    }
}
