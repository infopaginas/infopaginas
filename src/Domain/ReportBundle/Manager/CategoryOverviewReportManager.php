<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\Locality;
use Domain\BusinessBundle\Repository\CategoryRepository;
use Domain\BusinessBundle\Repository\LocalityRepository;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\PageBundle\Entity\Page;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\CategoryOverviewModel;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\MongoDbBundle\Manager\MongoDbManager;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Domain\BusinessBundle\Entity\Category;

class CategoryOverviewReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME_RAW = 'category_overview_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'category_overview_aggregate';
    const MONGO_DB_COLLECTION_NAME_POPULAR   = 'category_popular';

    const MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW = 'category_overview_archive_raw';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE = 'category_overview_archive_aggregate';

    const MONGO_DB_FIELD_ACTION = 'action';
    const MONGO_DB_FIELD_CATEGORY_ID = 'category_id';
    const MONGO_DB_FIELD_COUNT = 'count';
    const MONGO_DB_FIELD_DATE_TIME = 'datetime';
    const MONGO_DB_FIELD_TYPE = 'type';

    const MONGO_DB_FIELD_LOCALITY_ID = 'locality_id';


    const CATEGORY_TYPE_BUSINESS = 'business';
    const CATEGORY_TYPE_CATALOG  = 'catalog';
    const VISITORS = 'visitors';

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /**
     * CategoryOverviewReportManager constructor.
     * @param MongoDbManager $mongoDbManager
     */
    public function __construct(MongoDbManager $mongoDbManager)
    {
        $this->mongoDbManager = $mongoDbManager;
    }

    /**
     * @return LocalityRepository
     */
    protected function getLocalityRepository(): LocalityRepository
    {
        return $this->getEntityManager()->getRepository(Locality::class);
    }

    /**
     * @return CategoryRepository
     */
    protected function getCategoryRepository(): CategoryRepository
    {
        return $this->getEntityManager()->getRepository(Category::class);
    }

    /**
     * @param string $type
     * @param array $categoriesIds
     * @param integer $localityId
     *
     * @return bool
     */
    public function registerCategoriesInteractionEvent($type, array $categoriesIds, $localityId)
    {
        $data = $this->buildCategoriesInteractions($categoriesIds, $type, $localityId);

        if ($this->insertCategoriesInteractions($data)) {
            return true;
        }

        return false;
    }

    /**
     * @param int $categoryId
     * @param string $action
     * @param MongoDB\BSON\UTCDateTime $date
     * @param integer $localityId
     *
     * @return array
     */
    protected function buildSingleCategoryInteraction($categoryId, $action, $date, $localityId)
    {
        $data = [
            self::MONGO_DB_FIELD_CATEGORY_ID => (int)$categoryId,
            self::MONGO_DB_FIELD_ACTION => $action,
            self::MONGO_DB_FIELD_LOCALITY_ID => $localityId,
            self::MONGO_DB_FIELD_DATE_TIME => $date,
            self::MONGO_DB_FIELD_TYPE => BusinessOverviewModel::TYPE_CODE_CATEGORY_BUSINESS,
        ];

        return $data;
    }

    /**
     * @param array $categoriesIds
     * @param string $action
     * @param integer $localityId
     *
     * @return array
     */
    protected function buildCategoriesInteractions($categoriesIds, $action, $localityId)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        foreach ($categoriesIds as $categoryId) {
            $data[] = $this->buildSingleCategoryInteraction($categoryId, $action, $date, $localityId);
        }

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function insertCategoriesInteractions($data)
    {
        return $this->mongoDbManager->insertMany(self::MONGO_DB_COLLECTION_NAME_RAW, $data);
    }

    /**
     * @param ReportDatesRangeVO $period
     */
    public function aggregateCategoriesInteractions($period)
    {
        $this->mongoDbManager->createIndex(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, [
            self::MONGO_DB_FIELD_DATE_TIME => MongoDbManager::INDEX_TYPE_DESC,
        ]);

        $aggregateStartDate = $this->mongoDbManager->typeUTCDateTime($period->getStartDate());
        $aggregateEndDate = $this->mongoDbManager->typeUTCDateTime($period->getEndDate());

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
                            'cid' => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
                            'type' => '$' . self::MONGO_DB_FIELD_TYPE,
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$query',
                        self::MONGO_DB_FIELD_COUNT => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ]
        );

        $i = 0;
        $insert = [];

        foreach ($cursor as $document) {
            $document[self::MONGO_DB_FIELD_ACTION] = $document['_id']['action'];
            $document[self::MONGO_DB_FIELD_TYPE] = $document['_id']['type'];
            $document[self::MONGO_DB_FIELD_CATEGORY_ID] = $document['_id']['cid'];
            $document[self::MONGO_DB_FIELD_COUNT] = (int)$document[self::MONGO_DB_FIELD_COUNT];
            $document[self::MONGO_DB_FIELD_DATE_TIME] = $aggregateStartDate;

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
     * @param bool $paginated
     *
     * @return array
     */
    public function getCategoryDataFromMongo($params, $paginated = true)
    {
        if ($paginated) {
            $resultPagination = [
                [
                    '$skip' => (int)(($params['_page'] - 1) * $params['_per_page']),
                ],
                [
                    '$limit' => (int)$params['_per_page'],
                ],
            ];
        } else {
            $resultPagination = [
                [
                    '$skip' => 0,
                ],
            ];
        }

        $impressionQuery = $this->getBusinessCategoryEventSubQuery(CategoryOverviewModel::TYPE_CODE_IMPRESSION);
        $callMobQuery = $this->getBusinessCategoryEventSubQuery(CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON);
        $directionQuery = $this->getBusinessCategoryEventSubQuery(CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON);
        $visitorsQuery = $this->getCatalogCategoryEventSubQuery(CategoryOverviewModel::TYPE_CODE_IMPRESSION);

        $aggregationQuery = [
            [
                '$match' => $this->getMongoMatchQuery($params),
            ],
            [
                '$group' => [
                    '_id' => [
                        self::MONGO_DB_FIELD_CATEGORY_ID => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
                    ],
                    CategoryOverviewModel::TYPE_CODE_IMPRESSION => $impressionQuery,
                    CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON => $callMobQuery,
                    CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON => $directionQuery,
                    self::VISITORS => $visitorsQuery,
                ],
            ],
            [
                '$sort' => [
                    self::VISITORS => MongoDbManager::INDEX_TYPE_DESC,
                    '_id' => MongoDbManager::INDEX_TYPE_ASC,
                ],
            ],
            [
                '$facet' => [
                    'metadata' => [
                        [
                            '$count' => 'total',
                        ]
                    ],
                    'results' => $resultPagination,
                ],
            ],
        ];

        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            $aggregationQuery
        );

        $result = [];
        $total = 0;

        if ($cursor) {
            $data = current($cursor->toArray());

            if ($data && $data->results) {
                foreach ($data->results as $document) {
                    $categoryId = $document['_id'][self::MONGO_DB_FIELD_CATEGORY_ID];

                    $impressions = $document[CategoryOverviewModel::TYPE_CODE_IMPRESSION];
                    $callMob = $document[CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON];
                    $direction = $document[CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON];
                    $visitors = $document[self::VISITORS];

                    if (!isset($result[$categoryId])) {
                        $result[$categoryId] = [
                            CategoryOverviewModel::TYPE_CODE_IMPRESSION => $impressions,
                            CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON => $callMob,
                            CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON => $direction,
                            self::VISITORS => $visitors,
                        ];
                    } else {
                        $result[$categoryId][CategoryOverviewModel::TYPE_CODE_IMPRESSION] += $impressions;
                        $result[$categoryId][CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON] += $callMob;
                        $result[$categoryId][CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON] += $direction;
                        $result[$categoryId][self::VISITORS] += $visitors;
                    }
                }
            }

            if ($data && $data->metadata) {
                $metadata = current($data->metadata);

                if ($metadata) {
                    $total = $metadata['total'];
                }
            }
        }

        return [
            'result' => $result,
            'total' => $total,
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function getMongoMatchQuery($params)
    {
        $query = [];

        $query[self::MONGO_DB_FIELD_DATE_TIME] = [
            '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
            '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
        ];

        if (!empty($params['locality']['value'])) {
            $query[self::MONGO_DB_FIELD_LOCALITY_ID] = (int)$params['locality']['value'];
        }

        if (!empty($params['type']['value'])) {
            $query[self::MONGO_DB_FIELD_TYPE] = $params['type']['value'];
        }

        if (isset($params['categoriesSearch'])) {
            $query[self::MONGO_DB_FIELD_CATEGORY_ID] = [
                '$in' => $params['categoriesSearch'],
            ];
        }

        $query[self::MONGO_DB_FIELD_ACTION] = [
            '$in' => CategoryOverviewModel::getTypes(),
        ];

        return $query;
    }

    /**
     * @var string $event
     *
     * @return array
     */
    protected function getBusinessCategoryEventSubQuery($event)
    {
        return $this->getCategoryEventSubQuery($event, BusinessOverviewModel::TYPE_CODE_CATEGORY_BUSINESS);
    }

    /**
     * @var string $event
     *
     * @return array
     */
    protected function getCatalogCategoryEventSubQuery($event)
    {
        return $this->getCategoryEventSubQuery($event, BusinessOverviewModel::TYPE_CODE_CATEGORY_CATALOG);
    }

    /**
     * @var string $event
     * @var string $type
     *
     * @return array
     */
    protected function getCategoryEventSubQuery($event, $type)
    {
        $subQuery = [
            '$sum' => [
                '$cond' => [
                    [
                        '$and' => [
                            [
                                '$eq' => [
                                    '$' . self::MONGO_DB_FIELD_TYPE,
                                    $type
                                ],
                            ],
                            [
                                '$eq' => [
                                    '$' . self::MONGO_DB_FIELD_ACTION,
                                    $event
                                ],
                            ],
                        ],
                    ],
                    '$' . self::MONGO_DB_FIELD_COUNT,
                    0
                ],
            ],
        ];

        return $subQuery;
    }

    /**
     * @param string $type
     * @param array $data
     *
     * @return bool
     */
    public function registerCategoryEvent($type, $data)
    {
        foreach ($data as $items) {
            $this->saveCategoryEvent($type, $items);
        }

        return true;
    }

    /**
     * @param string $type
     * @param array $items
     */
    public function saveCategoryEvent($type, $items)
    {
        foreach ($items as $localityId => $categoryIds) {
            $data = $this->buildCatalogCategories($type, $categoryIds, $localityId);

            $this->insertBusinessCategories($data);
        }
    }

    /**
     * @param string $type
     * @param array $categoryIds
     * @param int $localityId
     *
     * @return array
     */
    protected function buildCatalogCategories($type, $categoryIds, $localityId)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        foreach ($categoryIds as $categoryId) {
            $data[] = $this->buildSingleBusinessCategory(
                $categoryId,
                $localityId,
                $type,
                $date
            );
        }

        return $data;
    }

    /**
     * @param int $categoryId
     * @param int $localityId
     * @param string $type
     * @param MongoDB\BSON\UTCDateTime $date
     *
     * @return array
     */
    protected function buildSingleBusinessCategory($categoryId, $localityId, $type, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_CATEGORY_ID => (int)$categoryId,
            self::MONGO_DB_FIELD_LOCALITY_ID => (int)$localityId,
            self::MONGO_DB_FIELD_TYPE => $type,
            self::MONGO_DB_FIELD_DATE_TIME => $date,
            self::MONGO_DB_FIELD_ACTION => BusinessOverviewModel::TYPE_CODE_IMPRESSION,
        ];

        return $data;
    }

    /**
     * @param array $data
     *
     */
    protected function insertBusinessCategories($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            $data
        );
    }

    public function updatePopularCategories()
    {
        $localities = $this->getLocalityRepository()->getAllLocalitiesIterator();

        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());
        $data = [];

        foreach ($localities as $row) {
            /* @var Locality $locality */
            $locality = current($row);

            $params = self::getPopularCategoryMongoSearchParams($locality->getId(), Page::POPULAR_CATEGORY_STAT_PERIOD);
            $categories = $this->getPopularCategoryRawData($params);

            if ($categories) {
                foreach ($categories as $categoryId => $stats) {
                    $count = $stats[self::VISITORS];

                    $data[] = $this->buildSinglePopularCategory(
                        $categoryId,
                        $locality->getId(),
                        $count,
                        $date
                    );
                }
            }
        }

        if ($data) {
            $this->insertPopularCategories($data);
        }
    }

    /**
     * @param int $localityId
     * @param string $period
     *
     * @return array
     */
    public static function getPopularCategoryMongoSearchParams($localityId, $period)
    {
        $params = [
            '_per_page' => Page::POPULAR_CATEGORY_COUNT,
            '_page' => Page::POPULAR_CATEGORY_PAGE,
            'dateObject' => DatesUtil::getDateRangeValueObjectFromRangeType($period),
            'locality' => [
                'value' => $localityId,
            ],
        ];

        return $params;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getPopularCategoryRawData(array $params = [])
    {
        $categoryResult = $this->getCategoryDataFromMongo($params);

        return $categoryResult['result'];
    }

    /**
     * @param int $categoryId
     * @param int $localityId
     * @param int $count
     * @param MongoDB\BSON\UTCDateTime $date
     *
     * @return array
     */
    protected function buildSinglePopularCategory($categoryId, $localityId, $count, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_CATEGORY_ID => $categoryId,
            self::MONGO_DB_FIELD_LOCALITY_ID => $localityId,
            self::MONGO_DB_FIELD_COUNT       => $count,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
        ];

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function insertPopularCategories($data)
    {
        $this->mongoDbManager->createIndex(self::MONGO_DB_COLLECTION_NAME_POPULAR, [
            self::MONGO_DB_FIELD_DATE_TIME   => MongoDbManager::INDEX_TYPE_DESC,
            self::MONGO_DB_FIELD_LOCALITY_ID => MongoDbManager::INDEX_TYPE_ASC,
        ]);

        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME_POPULAR,
            $data
        );
    }

    /**
     * @param \Datetime $date
     */
    public function archiveRawBusinessCategories($date)
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
    public function archiveAggregatedBusinessCategories($date)
    {
        $this->mongoDbManager->archiveCollection(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            self::MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE,
            self::MONGO_DB_FIELD_DATE_TIME,
            $date
        );
    }

    /**
     * @param \Datetime $date
     */
    public function deleteArchivedRawCategoryData($date)
    {
        $this->mongoDbManager->deleteOldData(
            self::MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW,
            self::MONGO_DB_FIELD_DATE_TIME,
            $date
        );
    }

    /**
     * @param array $params
     * @param bool $paginated
     *
     * @return array
     */
    public function getCategoryReportData(array $params = [], $paginated = true)
    {
        $params['dateObject'] = DatesUtil::getDateRangeVOFromDateString(
            $params['date']['value']['start'],
            $params['date']['value']['end']
        );

        if (isset($params['name']) && $params['name']['value']) {
            $categoriesSearch = $this->getCategoriesIdsByName($params['name']['value']);
            $params['categoriesSearch'] = $categoriesSearch;
        }

        $categoryOverviewResult = $this->getCategoryDataFromMongo(
            $params,
            $paginated
        );

        $stats = $categoryOverviewResult['result'];
        $categoryIds = array_keys($stats);
        $mapping = $this->getCategoryMapping($categoryIds);

        $data   = [];
        $labels = [];
        $counts = [];
        $impressions = [];
        $directions  = [];
        $callsMobile = [];

        foreach ($categoryIds as $categoryId) {
            if (!empty($mapping[$categoryId])) {
                $label = $mapping[$categoryId];
            } else {
                $label = $categoryId . ' - item was deleted';
            }

            $categoryOverviewData = $stats[$categoryId];

            $impression = $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_IMPRESSION];
            $callMobile = $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON];
            $direction  = $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON];
            $visitor    = $categoryOverviewData[self::VISITORS];

            // chart data
            $labels[]      = $label;
            $impressions[] = $impression;
            $callsMobile[] = $callMobile;
            $directions[]  = $direction;
            $counts[]      = $visitor;

            // table
            $data[] = [
                'name' => $label,
                CategoryOverviewModel::TYPE_CODE_IMPRESSION => $impression,
                CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON => $direction,
                CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON => $callMobile,
                'count' => $visitor,
            ];
        }

        $total = $categoryOverviewResult['total'];

        $currentPage = $params['_page'];
        $lastPage = ceil($total / $params['_per_page']);
        $nextPage = $lastPage;
        $previousPage = 1;

        if ($currentPage + 1 < $lastPage) {
            $nextPage = $currentPage + 1;
        }

        if ($currentPage - 1 > 1) {
            $previousPage = $currentPage - 1;
        }

        $rangePage = AdminHelper::getPageRanges($currentPage, $lastPage);

        $categoryData = [
            'results'      => $data,
            'labels'       => $labels,
            'impressions'  => $impressions,
            'directions'   => $directions,
            'callsMobile'  => $callsMobile,
            'counts'       => $counts,
            'total'        => $total,
            'currentPage'  => $currentPage,
            'lastPage'     => $lastPage,
            'nextPage'     => $nextPage,
            'previousPage' => $previousPage,
            'perPage'      => $params['_per_page'],
            'dates'        => $params['dateObject'],
            'rangePage'    => $rangePage,
        ];

        return $categoryData;
    }

    /**
     * @param array $categoryIds
     *
     * @return array
     */
    protected function getCategoryMapping($categoryIds)
    {
        $data = [];

        $categories = $this->getCategoryRepository()->getAvailableCategoryNameByIds($categoryIds);

        foreach ($categories as $category) {
            $data[$category['id']] = $category['name'];
        }

        return $data;
    }

    /**
     * @param string $searchName
     * @return array
     */
    private function getCategoriesIdsByName($searchName)
    {
        $categories = $this->getCategoryRepository()->getCategoriesByName($searchName);

        if ($categories) {
            return BusinessProfileUtil::extractEntitiesId($categories);
        }

        return [];
    }

    /**
     * @return array
     */
    public static function getCategoryPageType()
    {
        return [
            BusinessOverviewModel::TYPE_CODE_CATEGORY_BUSINESS => 'category_report.page_type.business',
            BusinessOverviewModel::TYPE_CODE_CATEGORY_CATALOG  => 'category_report.page_type.catalog',
        ];
    }

    /**
     * @param int $localityId
     *
     * @return array
     */
    public function getPopularCategoryData($localityId)
    {
        $params = self::getPopularCategoryMongoSearchParams($localityId, Page::POPULAR_CATEGORY_AGGREGATE_PERIOD);

        $categoryResult = $this->getPopularCategoryDataFromMongo($params);
        $categoryIds    = array_keys($categoryResult);

        return $categoryIds;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function getPopularCategoryDataFromMongo($params)
    {
        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_POPULAR,
            [
                [
                    '$match' => $this->getMongoMatchQuery($params),
                ],
                [
                    '$group' => [
                        '_id' => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
                        self::MONGO_DB_FIELD_COUNT => [
                            '$sum' => '$' . self::MONGO_DB_FIELD_COUNT,
                        ],
                    ],
                ],
                [
                    '$sort'  => [
                        self::MONGO_DB_FIELD_COUNT => MongoDbManager::INDEX_TYPE_DESC,
                        '_id' => MongoDbManager::INDEX_TYPE_ASC,
                    ],
                ],
                [
                    '$group' => [
                        '_id'   => null,
                        'total' => [
                            '$sum' => 1,
                        ],
                        'results' => [
                            '$push' => '$$ROOT',
                        ]
                    ],
                ],
                [
                    '$project' => [
                        'total' => 1,
                        'results' => [
                            '$slice' => [
                                '$results',
                                (int)(($params['_page'] - 1) * $params['_per_page']),
                                (int)$params['_per_page'],
                            ]
                        ]
                    ],
                ],
            ]
        );

        $result = [];

        $data = current($cursor->toArray());

        if ($data) {
            foreach ($data->results as $document) {
                $result[$document['_id']] = $document[self::MONGO_DB_FIELD_COUNT];
            }
        }

        return $result;
    }
}
