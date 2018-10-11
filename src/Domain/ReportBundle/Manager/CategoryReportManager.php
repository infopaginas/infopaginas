<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\Category;
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

class CategoryReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME_RAW       = 'category_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'category_aggregate';
    const MONGO_DB_COLLECTION_NAME_POPULAR   = 'category_popular';

    const MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW       = 'category_archive_raw';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE = 'category_archive_aggregate';

    const MONGO_DB_FIELD_CATEGORY_ID = 'category_id';
    const MONGO_DB_FIELD_LOCALITY_ID = 'locality_id';
    const MONGO_DB_FIELD_PAGE_TYPE   = 'type';
    const MONGO_DB_FIELD_COUNT       = 'count';
    const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

    const CATEGORY_TYPE_BUSINESS = 'business';
    const CATEGORY_TYPE_CATALOG  = 'catalog';

    protected $reportName = 'category_report';

    public function __construct(MongoDbManager $mongoDbManager)
    {
        $this->mongoDbManager = $mongoDbManager;
    }

    /**
     * @return array|\Domain\BusinessBundle\Entity\Category[]
     */
    public function getCategories()
    {
        return $this->getCategoryRepository()->findAll();
    }

    private function getCategoriesIdsByName($searchName)
    {
        $categories = $this->getCategoryRepository()->getCategoriesByName($searchName);

        if ($categories) {
            return BusinessProfileUtil::extractEntitiesId($categories);
        }

        return [];
    }

    /**
     * @return CategoryOverviewReportManager
     */
    private function getCategoryOverviewReportManager()
    {
        return $this->getContainer()
            ->get('domain_report.manager.category_overview_report_manager');
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

        $categoryOverviewManager = $this->getCategoryOverviewReportManager();

        $categoryOverviewResult = $categoryOverviewManager->getCategoryDataFromMongo(
            $params,
            $paginated
        );

        $stats = $categoryOverviewResult['result'];
        $total = $categoryOverviewResult['total'];
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
                $categoryOverviewData = $stats[$categoryId];
                $label = $mapping[$categoryId];

                // chart data
                $labels[]      = $label;
                $impressions[] = $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_IMPRESSION];
                $callsMobile[] = $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON];
                $directions[]  = $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON];
                $counts[]      = $categoryOverviewData[CategoryOverviewReportManager::VISITORS];

                // table
                $data[] = [
                    'name'  => $label,
                    CategoryOverviewModel::TYPE_CODE_IMPRESSION =>
                        $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_IMPRESSION],
                    CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON =>
                        $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON],
                    CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON =>
                        $categoryOverviewData[CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON],
                    'count' => $categoryOverviewData[CategoryOverviewReportManager::VISITORS],
                ];
            }
        }

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
     * @return array
     */
    public function getPopularCategoryRawData(array $params = [])
    {
        $categoryResult = $this->getCategoryDataFromMongo($params);

        return $categoryResult['result'];
    }

    /**
     * @param string $type
     * @param array  $data
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
     * @param array  $items
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
     * @param array  $categoryIds
     * @param int    $localityId
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
     * @param string $type
     *
     * @return string
     */
    protected function getMongoCategoryType($type)
    {
        if ($type == BusinessOverviewModel::TYPE_CODE_CATEGORY_BUSINESS) {
            $mongoType = self::CATEGORY_TYPE_BUSINESS;
        } else {
            $mongoType = self::CATEGORY_TYPE_CATALOG;
        }

        return $mongoType;
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
            self::MONGO_DB_FIELD_PAGE_TYPE   => $type,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
        ];

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function insertBusinessCategories($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            $data
        );
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

    /**
     * @param array $params
     * @param bool $paginated
     *
     * @return array
     */
    protected function getCategoryDataFromMongo($params, $paginated = true)
    {
        $aggregationQuery = [
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
                '$sort' => [
                    self::MONGO_DB_FIELD_COUNT => MongoDbManager::INDEX_TYPE_DESC,
                ],
            ],
            [
                '$group' => [
                    '_id' => null,
                    'total' => [
                        '$sum' => 1,
                    ],
                    'results' => [
                        '$push' => '$$ROOT',
                    ],
                ],
            ],
        ];


        $pagination = [
            '$project' => [
                'total' => 1,
                'results' => [
                    '$slice' => [
                        '$results',
                        (int)(($params['_page'] - 1) * $params['_per_page']),
                        (int)$params['_per_page'],
                    ],
                ],
            ],
        ];

        if ($paginated) {
            array_push($aggregationQuery, $pagination);
        }

        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            $aggregationQuery
        );

        $result = [];
        $total  = 0;

        if ($cursor) {
            $data = current($cursor->toArray());

            if ($data) {
                foreach ($data->results as $document) {
                    $result[$document['_id']] = $document[self::MONGO_DB_FIELD_COUNT];
                }

                $total = $data->total;
            }
        }

        return [
            'result' => $result,
            'total' => $total,
        ];
    }

    /**
     * @param ReportDatesRangeVO $period
     */
    public function aggregateBusinessCategories($period)
    {
        $this->mongoDbManager->createIndex(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, [
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
                            'cid'  => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
                            'lid'  => '$' . self::MONGO_DB_FIELD_LOCALITY_ID,
                            'type' => '$' . self::MONGO_DB_FIELD_PAGE_TYPE,
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
            $document[self::MONGO_DB_FIELD_CATEGORY_ID] = $document['_id']['cid'];
            $document[self::MONGO_DB_FIELD_LOCALITY_ID] = $document['_id']['lid'];
            $document[self::MONGO_DB_FIELD_PAGE_TYPE]   = $document['_id']['type'];
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
                foreach ($categories as $categoryId => $count) {
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
            $query[self::MONGO_DB_FIELD_PAGE_TYPE] = $params['type']['value'];
        }

        if (isset($params['categoriesSearch'])) {
            $query[self::MONGO_DB_FIELD_CATEGORY_ID] = [
                '$in' => $params['categoriesSearch'],
            ];
        }

        return $query;
    }

    /**
     * @param array $categoryIds
     *
     * @return array
     */
    protected function getCategoryMapping($categoryIds)
    {
        $data = [];

        $categories = $this->getCategoryRepository()->getAvailableCategoriesByIds($categoryIds);

        foreach ($categories as $category) {
            $data[$category->getId()] = $category->getName();
        }

        return $data;
    }

    /**
     * @return CategoryRepository
     */
    protected function getCategoryRepository() : CategoryRepository
    {
        return $this->getEntityManager()->getRepository(Category::class);
    }

    /**
     * @return LocalityRepository
     */
    protected function getLocalityRepository() : LocalityRepository
    {
        return $this->getEntityManager()->getRepository(Locality::class);
    }

    /**
     * @return array
     */
    public static function getCategoryPageType()
    {
        return [
            self::CATEGORY_TYPE_BUSINESS => 'category_report.page_type.business',
            self::CATEGORY_TYPE_CATALOG  => 'category_report.page_type.catalog',
        ];
    }

    /**
     * @param int       $localityId
     * @param string    $period
     *
     * @return array
     */
    public static function getPopularCategoryMongoSearchParams($localityId, $period)
    {
        $params = [
            '_per_page' => Page::POPULAR_CATEGORY_COUNT,
            '_page'     => Page::POPULAR_CATEGORY_PAGE,
            'dateObject' => DatesUtil::getDateRangeValueObjectFromRangeType($period),
            'locality' => [
                'value' => $localityId,
            ],
        ];

        return $params;
    }
}
