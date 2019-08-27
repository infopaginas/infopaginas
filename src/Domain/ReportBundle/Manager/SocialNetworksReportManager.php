<?php

namespace Domain\ReportBundle\Manager;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Domain\ReportBundle\Util\DatesUtil;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

/**
 * Class SocialNetworksReportManager
 * @package Domain\ReportBundle\Manager
 */
class SocialNetworksReportManager
{
    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    const MONGO_DB_COLLECTION_NAME_RAW       = 'overview_raw';
    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'overview_aggregate';

    const MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW       = 'overview_archive_raw';
    const MONGO_DB_COLLECTION_NAME_ARCHIVE_AGGREGATE = 'overview_archive_aggregate';

    const MONGO_DB_FIELD_ACTION    = 'action';
    const MONGO_DB_FIELD_BUSINESS_ID = 'business_id';
    const MONGO_DB_FIELD_COUNT       = 'count';
    const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

    /**
     * @param MongoDbManager $mongoDbManager
     */
    public function __construct(MongoDbManager $mongoDbManager)
    {
        $this->mongoDbManager = $mongoDbManager;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getSocialNetworkData(array $params = [])
    {
        $stats = $this->getSocialNetworksDataFromMongoDb($params);

        $keywordsData = [
            'results'  => $stats,
            'socialNetworks' => array_keys($stats),
            'clicks' => array_values($stats),
        ];

        return $keywordsData;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function registerBusinessKeywordEvent($data)
    {
        foreach ($data as $search => $businessIds) {
            $this->saveProfilesDataSuggestedBySearchQuery($search, $businessIds);
        }

        return true;
    }

    /**
     * @param string $search
     * @param array  $businessIds
     */
    public function saveProfilesDataSuggestedBySearchQuery($search, $businessIds)
    {
        $keywords = mb_strtolower($search);

        $data = $this->buildBusinessSocialNetworks($keywords, $businessIds);

        $this->insertBusinessKeywords($data);
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    protected function getSocialNetworksDataFromMongoDb(array $params)
    {
        $params['dateObject'] = DatesUtil::getDateRangeVOFromDateString(
            $params['date']['start'],
            $params['date']['end']
        );

        return $this->getBusinessSocialNetworksData($params);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function getBusinessSocialNetworksData($params)
    {
        $socialNetworksActions = array(
            BusinessOverviewModel::TYPE_CODE_FACEBOOK_VISIT,
            BusinessOverviewModel::TYPE_CODE_INSTAGRAM_VISIT,
            BusinessOverviewModel::TYPE_CODE_YOUTUBE_VISIT,
            BusinessOverviewModel::TYPE_CODE_GOOGLE_VISIT,
            BusinessOverviewModel::TYPE_CODE_LINKED_IN_VISIT,
            BusinessOverviewModel::TYPE_CODE_TWITTER_VISIT,
            BusinessOverviewModel::TYPE_CODE_TRIP_ADVISOR_VISIT,
        );

        $cursor = $this->mongoDbManager->aggregateData(
            self::MONGO_DB_COLLECTION_NAME_AGGREGATE,
            [
                [
                    '$match' => [
                        self::MONGO_DB_FIELD_BUSINESS_ID => (int)$params['businessProfileId'],
                        self::MONGO_DB_FIELD_ACTION => [
                            '$in' => $socialNetworksActions
                        ],
                        self::MONGO_DB_FIELD_DATE_TIME => [
                            '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
                            '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$' . self::MONGO_DB_FIELD_ACTION,
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

    /**
     * @param int       $businessId
     * @param string    $socialNetwork
     * @param MongoDB\BSON\UTCDateTime $date
     *
     * @return array
     */
    protected function buildSingleBusinessSocialNetwork($businessId, $socialNetwork, $date)
    {
        $data = [
            self::MONGO_DB_FIELD_BUSINESS_ID => (int)$businessId,
            self::MONGO_DB_FIELD_ACTION     => $socialNetwork,
            self::MONGO_DB_FIELD_DATE_TIME   => $date,
        ];

        return $data;
    }

    /**
     * @param string $keywords
     * @param array  $businessIds
     *
     * @return array
     */
    protected function buildBusinessSocialNetworks($socialNetwork, $businessIds)
    {
        $data = [];
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        foreach ($businessIds as $businessId) {
            $data[] = $this->buildSingleBusinessSocialNetwork($businessId, $socialNetwork, $date);
        }

        $data[] = $this->buildSingleBusinessSocialNetwork(0, $socialNetwork, $date);

        return $data;
    }

    /**
     * @param array $data
     */
    protected function insertBusinessKeywords($data)
    {
        $this->mongoDbManager->insertMany(
            self::MONGO_DB_COLLECTION_NAME_RAW,
            $data
        );
    }

    /**
     * @param ReportDatesRangeVO $period
     */
    public function aggregateBusinessSocialNetworks($period)
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
            $document[self::MONGO_DB_FIELD_ACTION]     = $document['_id']['action'];
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
     * @param \Datetime $date
     */
    public function archiveRawBusinessSocialNetworks($date)
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
    public function archiveAggregatedBusinessSocialNetworks($date)
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
    public function deleteArchivedRawSocialNetworksData($date)
    {
        $this->mongoDbManager->deleteOldData(
            self::MONGO_DB_COLLECTION_NAME_ARCHIVE_RAW,
            self::MONGO_DB_FIELD_DATE_TIME,
            $date
        );
    }
}
