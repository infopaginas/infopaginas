<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Repository\CategoryRepository;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

class CategoryReportManager extends BaseReportManager
{
    const MONGO_DB_COLLECTION_NAME_RAW       = 'category_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'category_aggregate';

    const MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW       = 'category_archive_raw';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE = 'category_archive_aggregate';

    const MONGO_DB_FIELD_CATEGORY_ID = 'category_id';
    const MONGO_DB_FIELD_COUNT       = 'count';
    const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

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

    /**
     * @param array $params
     * @return array
     */
    public function getCategoryReportData(array $params = [])
    {
        $params['dateObject'] = DatesUtil::getDateRangeVOFromDateString(
            $params['date']['value']['start'],
            $params['date']['value']['end']
        );

        $categoryResult = $this->getCategoryDataFromMongo($params);

        $stats = $categoryResult['result'];
        $total = $categoryResult['total'];

        $categoryIds = array_keys($stats);

        $mapping = $this->getCategoryMapping($categoryIds);

        $data   = [];
        $labels = [];
        $counts = [];

        foreach ($categoryIds as $categoryId) {
            if (!empty($mapping[$categoryId])) {
                $label = $mapping[$categoryId];
                $count = $stats[$categoryId];

                // chart data
                $labels[] = $label;
                $counts[] = $count;

                // table
                $data[] = [
                    'name'  => $label,
                    'count' => $count,
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

        $categoryData = [
            'results'      => $data,
            'labels'       => $labels,
            'counts'       => $counts,
            'total'        => $total,
            'currentPage'  => $currentPage,
            'lastPage'     => $lastPage,
            'nextPage'     => $nextPage,
            'previousPage' => $previousPage,
            'perPage'      => $params['_per_page'],
            'dates'        => $params['dateObject'],
        ];

        return $categoryData;
    }

    /**
     * @param BusinessProfile $businessProfile
     */
    public function registerBusinessVisit(BusinessProfile $businessProfile)
    {
        $data = $this->buildBusinessCategories($businessProfile);

        $this->insertBusinessCategories($data);
    }

    protected function buildBusinessCategories(BusinessProfile $businessProfile)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        foreach ($businessProfile->getCategories() as $category) {
            $data[] = $this->buildSingleBusinessCategory($category->getId(), $date);
        }

        return $data;
    }

    protected function buildSingleBusinessCategory($categoryId, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_CATEGORY_ID => $categoryId,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
        ];

        return $data;
    }

    protected function insertBusinessCategories($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            $data
        );
    }

    protected function getCategoryDataFromMongo($params)
    {
        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
                [
                    '$match' => [
                        self::MONGO_DB_FIELD_DATE_TIME => [
                            '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
                            '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
                        ],
                    ],
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
                        self::MONGO_DB_FIELD_COUNT => -1,
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
        $total  = 0;

        $data = current($cursor->toArray());

        if ($data) {
            foreach ($data->results as $document) {
                $result[$document['_id']] = $document[self::MONGO_DB_FIELD_COUNT];
            }

            $total = $data->total;
        }

        return [
            'result' => $result,
            'total'  => $total,
        ];
    }

    public function aggregateBusinessCategories($period)
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
                    '$group' => [
                        '_id' => '$' . self::MONGO_DB_FIELD_CATEGORY_ID,
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
            $document[self::MONGO_DB_FIELD_CATEGORY_ID] = $document['_id'];
            $document[self::MONGO_DB_FIELD_DATE_TIME]   = $aggregateStartDate;

            $document['_id'] = $this->mongoDbManager->generateId();

            $insert[] = $document;

            if (($i % MongoDbManager::DEFAULT_BATCH_SIZE) === 0) {
                $this->mongoDbManager->insertMany(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, $insert);
                $insert = [];
            }
        }

        if ($insert) {
            $this->mongoDbManager->insertMany(self::MONGO_DB_COLLECTION_NAME_AGGREGATE, $insert);
        }
    }

    /**
     * @param $date \Datetime
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
     * @param $date \Datetime
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

    protected function getCategoryMapping($categoryIds)
    {
        $data = [];

        $categories = $this->getCategoryRepository()->getAvailableCategoriesByIds($categoryIds);

        foreach ($categories as $category) {
            $data[$category->getId()] = $category->getName();
        }

        return $data;
    }

    protected function getCategoryRepository() : CategoryRepository
    {
        return $this->getEntityManager()->getRepository(Category::class);
    }
}
