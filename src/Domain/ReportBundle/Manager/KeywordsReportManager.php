<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Service\StemmerService;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

/**
 * Class KeywordsReportManager
 * @package Domain\ReportBundle\Manager
 */
class KeywordsReportManager
{
    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    const KEYWORDS_PER_PAGE_COUNT = [
        5   => 5,
        10  => 10,
        15  => 15,
        20  => 20,
        25  => 25,
        50  => 50,
        100 => 100,
        500 => 500,
    ];

    const DEFAULT_KEYWORDS_COUNT = 15;

    const MONGO_DB_COLLECTION_NAME_RAW       = 'keyword_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'keyword_aggregate';

    const MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW       = 'keyword_archive_raw';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE = 'keyword_archive_aggregate';

    const MONGO_DB_FIELD_KEYWORD     = 'keyword';
    const MONGO_DB_FIELD_BUSINESS_ID = 'business_id';
    const MONGO_DB_FIELD_COUNT       = 'count';
    const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

    public function __construct(MongoDbManager $mongoDbManager)
    {
        $this->mongoDbManager = $mongoDbManager;
    }

    public function getKeywordsData(array $params = [])
    {
        $stats = $this->getKeywordsDataFromMongoDb($params);

        $keywordsData = [
            'results'  => $stats,
            'keywords' => array_keys($stats),
            'searches' => array_values($stats),
        ];

        return $keywordsData;
    }

    /**
     * @param string $search
     * @param BusinessProfile[] $businessProfiles
     */
    public function saveProfilesDataSuggestedBySearchQuery($search, $businessProfiles)
    {
        $keywords = mb_strtolower($search);

        $data = $this->buildBusinessKeywords($businessProfiles, $keywords);

        $this->insertBusinessKeywords($data);
    }

    /**
     * @param array $params
     * @return mixed
     */
    protected function getKeywordsDataFromMongoDb(array $params)
    {
        $params['dateObject'] = DatesUtil::getDateRangeVOFromDateString(
            $params['date']['start'],
            $params['date']['end']
        );

        return $this->getBusinessKeywordsData($params);
    }

    protected function getBusinessKeywordsData($params)
    {
        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
                [
                    '$match' => [
                        self::MONGO_DB_FIELD_BUSINESS_ID => (int)$params['businessProfileId'],
                        self::MONGO_DB_FIELD_DATE_TIME => [
                            '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
                            '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$' . self::MONGO_DB_FIELD_KEYWORD,
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
                    '$limit' => (int)$params['limit'],
                ],
            ]
        );

        $result = [];

        foreach ($cursor as $document) {
            $result[$document['_id']] = $document[self::MONGO_DB_FIELD_COUNT];
        }

        return $result;
    }

    protected function buildSingleBusinessKeyword($businessId, $keyword, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_BUSINESS_ID => $businessId,
            self::MONGO_DB_FIELD_KEYWORD     => $keyword,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
        ];

        return $data;
    }

    protected function buildBusinessKeywords($businessProfiles, $keywords)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        foreach ($businessProfiles as $businessProfile) {
            $data[] = $this->buildSingleBusinessKeyword($businessProfile->getId(), $keywords, $date);
        }

        $data[] = $this->buildSingleBusinessKeyword(0, $keywords, $date);

        return $data;
    }

    protected function insertBusinessKeywords($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            $data
        );
    }

    public function aggregateBusinessKeywords($period)
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
                            'keyword' => '$' . self::MONGO_DB_FIELD_KEYWORD,
                            'bid'     => '$' . self::MONGO_DB_FIELD_BUSINESS_ID,
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
            $document[self::MONGO_DB_FIELD_KEYWORD]     = $document['_id']['keyword'];
            $document[self::MONGO_DB_FIELD_BUSINESS_ID] = $document['_id']['bid'];
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
     * @param $date \Datetime
     */
    public function archiveRawBusinessKeywords($date)
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
    public function archiveAggregatedBusinessKeywords($date)
    {
        $this->mongoDbManager->archiveCollection(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            self::MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE,
            self::MONGO_DB_FIELD_DATE_TIME,
            $date
        );
    }
}
