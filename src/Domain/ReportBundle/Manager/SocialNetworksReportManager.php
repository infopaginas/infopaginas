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

    const MONGO_DB_COLLECTION_NAME_AGGREGATE = 'overview_aggregate';

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
                        BusinessOverviewReportManager::MONGO_DB_FIELD_BUSINESS_ID => (int)$params['businessProfileId'],
                        BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION => [
                            '$in' => $socialNetworksActions
                        ],
                        BusinessOverviewReportManager::MONGO_DB_FIELD_DATE_TIME => [
                            '$gte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getStartDate()),
                            '$lte' => $this->mongoDbManager->typeUTCDateTime($params['dateObject']->getEndDate()),
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_ACTION,
                        BusinessOverviewReportManager::MONGO_DB_FIELD_COUNT => [
                            '$sum' => '$' . BusinessOverviewReportManager::MONGO_DB_FIELD_COUNT,
                        ],
                    ],
                ],
                [
                    '$sort'  => [
                        BusinessOverviewReportManager::MONGO_DB_FIELD_COUNT => -1,
                    ],
                ],
                [
                    '$limit' => (int)$params['limit'],
                ],
            ]
        );

        $result = [];
        foreach ($cursor as $document) {
            $result[$document['_id']] = $document[BusinessOverviewReportManager::MONGO_DB_FIELD_COUNT];
        }

        return $result;
    }
}
