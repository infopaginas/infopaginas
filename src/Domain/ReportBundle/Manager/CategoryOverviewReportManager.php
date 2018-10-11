<?php

namespace Domain\ReportBundle\Manager;

use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\CategoryOverviewModel;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

class CategoryOverviewReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME_RAW       = 'category_overview_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'category_overview_aggregate';

    const MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW       = 'category_overview_archive_raw';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE = 'category_overview_archive_aggregate';

    const MONGO_DB_FIELD_ACTION                  = 'action';
    const MONGO_DB_FIELD_CATEGORY_ID             = 'category_id';
    const MONGO_DB_FIELD_COUNT                   = 'count';
    const MONGO_DB_FIELD_DATE_TIME               = 'datetime';
    const MONGO_DB_FIELD_TYPE                    = 'type';
    const MONGO_DB_FIELD_CATALOG_LOCALITY        = 'catalog_locality';

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
     * @param int       $categoryId
     * @param string    $action
     * @param MongoDB\BSON\UTCDateTime $date
     * @param integer $localityId
     *
     * @return array
     */
    protected function buildSingleCategoryInteraction($categoryId, $action, $date, $localityId)
    {
        $data = [
            self::MONGO_DB_FIELD_CATEGORY_ID      => (int) $categoryId,
            self::MONGO_DB_FIELD_ACTION           => $action,
            self::MONGO_DB_FIELD_CATALOG_LOCALITY => $localityId,
            self::MONGO_DB_FIELD_DATE_TIME        => $date,
            self::MONGO_DB_FIELD_TYPE             => BusinessOverviewModel::TYPE_CODE_CATEGORY_BUSINESS,
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
     * @param bool $paginated
     *
     * @return array
     */
    public function getCategoryDataFromMongo($params, $paginated = true)
    {
        $aggregationQuery = [
            [
                '$match' => $this->getMongoMatchQuery($params),
            ],
            [
                '$group' => [
                    '_id' => [
                        self::MONGO_DB_FIELD_CATEGORY_ID => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
                        self::MONGO_DB_FIELD_ACTION => '$' . self::MONGO_DB_FIELD_ACTION,
                        self::MONGO_DB_FIELD_CATALOG_LOCALITY => '$' . self::MONGO_DB_FIELD_CATALOG_LOCALITY,
                        self::MONGO_DB_FIELD_TYPE => '$' . self::MONGO_DB_FIELD_TYPE,
                    ],
                    self::MONGO_DB_FIELD_COUNT => [
                        '$sum' => '$' . self::MONGO_DB_FIELD_COUNT,
                    ],
                ],
            ],
            [
                '$sort' => [
                    self::MONGO_DB_FIELD_COUNT => MongoDbManager::INDEX_TYPE_DESC,
                    '_id' => MongoDbManager::INDEX_TYPE_ASC,
                ],
            ],
        ];

        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            $aggregationQuery
        );

        $result = [];

        if ($cursor) {
            foreach($cursor as $document){
                $count = $document[self::MONGO_DB_FIELD_COUNT];
                $document = $document['_id'];

                if (!isset($result[$document[self::MONGO_DB_FIELD_CATEGORY_ID]])) {
                    $result[$document[self::MONGO_DB_FIELD_CATEGORY_ID]] = [
                        CategoryOverviewModel::TYPE_CODE_IMPRESSION => 0,
                        CategoryOverviewModel::TYPE_CODE_CALL_MOB_BUTTON => 0,
                        CategoryOverviewModel::TYPE_CODE_DIRECTION_BUTTON => 0,
                        self::VISITORS => 0,
                    ];
                }

                if ($document[self::MONGO_DB_FIELD_TYPE] == BusinessOverviewModel::TYPE_CODE_CATEGORY_CATALOG) {
                    $result[$document[self::MONGO_DB_FIELD_CATEGORY_ID]]['visitors'] += 1;
                } else {
                    $result[$document[self::MONGO_DB_FIELD_CATEGORY_ID]][$document[self::MONGO_DB_FIELD_ACTION]] =
                        $count;
                    $result[$document[self::MONGO_DB_FIELD_CATEGORY_ID]]['visitors'] += 1;
                }
            }

        }

        if ($paginated) {
            $result = array_slice(
                $result,
                (int)(($params['_page'] - 1) * $params['_per_page']),
                (int)$params['_per_page'],
                true
            );
        }

        return [
            'result' => $result,
            'total' => count($result),
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
            $query[self::MONGO_DB_FIELD_CATALOG_LOCALITY] = (int)$params['locality']['value'];
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
     * @param string $type
     * @param array  $data
     * @param string $action
     *
     * @return bool
     */
    public function registerCategoryEvent($type, $data, $action)
    {
        foreach ($data as $items) {
            $this->saveCategoryEvent($type, $items, $action);
        }

        return true;
    }

    /**
     * @param string $type
     * @param array  $items
     * @param string $action
     */
    public function saveCategoryEvent($type, $items, $action)
    {
        foreach ($items as $localityId => $categoryIds) {
            $data = $this->buildCatalogCategories($type, $categoryIds, $localityId, $action);

            $this->insertBusinessCategories($data);
        }
    }

    /**
     * @param string $type
     * @param array  $categoryIds
     * @param int    $localityId
     * @param string $action
     *
     * @return array
     */
    protected function buildCatalogCategories($type, $categoryIds, $localityId, $action)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        foreach ($categoryIds as $categoryId) {
            $data[] = $this->buildSingleBusinessCategory(
                $categoryId,
                $localityId,
                $type,
                $date,
                $action
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
    protected function buildSingleBusinessCategory($categoryId, $localityId, $type, $date, $action)
    {
        $data = [
            self::MONGO_DB_FIELD_CATEGORY_ID => (int)$categoryId,
            self::MONGO_DB_FIELD_CATALOG_LOCALITY => (int)$localityId,
            self::MONGO_DB_FIELD_TYPE   => $type,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
            self::MONGO_DB_FIELD_ACTION => $action,
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
}
