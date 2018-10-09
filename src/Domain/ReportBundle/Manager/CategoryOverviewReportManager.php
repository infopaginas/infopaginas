<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Model\CategoryOverviewModel;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use phpDocumentor\Reflection\Types\Self_;

class CategoryOverviewReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME_RAW       = 'category_overview_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'category_overview_aggregate';

    const MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW       = 'category_overview_archive_raw';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE = 'category_overview_archive_aggregate';

    const MONGO_DB_FIELD_ACTION      = 'action';
    const MONGO_DB_FIELD_CATEGORY_ID = 'category_id';
    const MONGO_DB_FIELD_COUNT       = 'count';
    const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

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
     * @param string $type
     * @param array $categoriesIds
     *
     * @return bool
     */
    public function registerCategoriesInteractionEvent($type, array $categoriesIds)
    {
        $data = $this->buildCategoriesInteractions($categoriesIds, $type);

        $this->insertCategoriesInteractions($data);

        return true;
    }


    /**
     * @param int       $categoryId
     * @param string    $action
     * @param MongoDB\BSON\UTCDateTime $date
     *
     * @return array
     */
    protected function buildSingleCategoryInteraction($categoryId, $action, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_CATEGORY_ID => (int) $categoryId,
            self::MONGO_DB_FIELD_ACTION      => $action,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
        ];

        return $data;
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
     * @param array $categoriesIds
     * @param string $action
     *
     * @return array
     */
    protected function buildCategoriesInteractions($categoriesIds, $action)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        foreach ($categoriesIds as $categoryId) {
            $data[] = $this->buildSingleCategoryInteraction($categoryId, $action, $date);
        }

        $data[] = $this->buildSingleCategoryInteraction(0, $action, $date);

        return $data;
    }

    /**
     * @param array $data
     */
    protected function insertCategoriesInteractions($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            $data
        );
    }

    /**
     * @param ReportDatesRangeVO $period
     */
    public function aggregateCategoriesInteractions($period)
    {
        $this->mongoDbManager->createIndex(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, [
            self::MONGO_DB_FIELD_CATEGORY_ID => MongoDbManager::INDEX_TYPE_ASC,
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
                            'cid'    => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
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
            $document[self::MONGO_DB_FIELD_CATEGORY_ID] = $document['_id']['cid'];
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
    public function archiveCategoryInteractions($date)
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
    public function archiveAggregatedCategoryInteractions($date)
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
     * @param array $categoriesIds
     * @param array $actions
     * @param \DateTime $startDate
     * @param \DateTime|null $endDate
     * @return mixed
     */
    protected function getSummaryByAction($categoriesIds, $actions, \DateTime $startDate, \DateTime $endDate = null)
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
                        self::MONGO_DB_FIELD_CATEGORY_ID => [
                            '$in' => $categoriesIds,
                        ],
                        self::MONGO_DB_FIELD_DATE_TIME => $dateFilterMongo,
                    ],
                ],
                [
                    '$group' => [
                        '_id' => [
                            '_id' => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
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
     * @param array $categoriesIds
     * @param array $actions
     * @param \DateTime $startDate
     * @param \DateTime|null $endDate
     * @return array
     */
    public function getSummaryByActionData($categoriesIds, $actions, \DateTime $startDate, \DateTime $endDate = null)
    {
        $data = [];

        if (!$categoriesIds || !$actions) {
            return $data;
        }

        $cursor = $this->getSummaryByAction($categoriesIds, $actions, $startDate, $endDate);

        if ($cursor) {
            foreach ($cursor as $item) {
                $categoryId = $item['_id']['_id'];
                $action = $item['_id']['action'];

                if (isset($data[$categoryId][$action])) {
                    $data[$categoryId][$action] += $item['count'];
                } else {
                    $data[$categoryId][$action] = $item['count'];
                }
            }
        }

        return $data;
    }

    public function getCategoriesOverviewData($params, $categoryIds)
    {
        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
                [
                    '$match' => [
                        self::MONGO_DB_FIELD_CATEGORY_ID => [
                            '$in' => $categoryIds,
                        ],
                        self::MONGO_DB_FIELD_DATE_TIME => [
                            '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
                            '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => [
                            '_id' => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
                            self::MONGO_DB_FIELD_ACTION => '$' . self::MONGO_DB_FIELD_ACTION,
                        ],
                        self::MONGO_DB_FIELD_COUNT => [
                            '$sum' => '$' . self::MONGO_DB_FIELD_COUNT,
                        ],
                    ],
                ],
            ]
        );

        $exampleArray = [
            CategoryOverviewModel::TYPE_CODE_IMPRESSION => 0,
            CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON => 0,
            CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON => 0,
        ];

        $data = [];

        foreach ($categoryIds as $id) {
            $data[$id] = $exampleArray;
        }

        if ($cursor) {
            foreach ($cursor as $item) {
                $categoryId = $item['_id']['_id'];
                $action = $item['_id']['action'];

                if (isset($data[$categoryId][$action])) {
                    $data[$categoryId][$action] += $item['count'];
                } else {
                    $data[$categoryId][$action] = $item['count'];
                }
            }
        }

        return $data;
    }
}
