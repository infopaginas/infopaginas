<?php

namespace Domain\SiteBundle\Logger;

use Monolog\Logger;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

class CronLogger
{
    const MONGO_DB_COLLECTION_NAME   = 'cron_log';

    const MONGO_DB_FIELD_ACTION      = 'action';
    const MONGO_DB_FIELD_STATUS      = 'status';
    const MONGO_DB_FIELD_MESSAGE     = 'message';
    const MONGO_DB_FIELD_LEVEL       = 'level';
    const MONGO_DB_FIELD_PROCESS_ID  = 'process_id';
    const MONGO_DB_FIELD_CPU_USAGE   = 'cpu_usage';
    const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

    const ELASTIC_SYNC              = 'data:elastic:sync';
    const YOUTUBE_UPLOAD            = 'data:youtube-video:upload';
    const WORKING_HOURS_CONVERT     = 'data:working-hours:convert';
    const WORKING_HOURS_UPDATE      = 'data:working-hours:update';
    const DOUBLE_CLICK_SYNC         = 'ipgn:dfp:synch';
    const BUSINESS_UPDATE_STATUS    = 'domain:business:update-status';
    const SUBSCRIPTION_TRACK        = 'ipgn:subscriptions:track';
    const CATALOG_ITEM_UPDATE       = 'domain:business:catalog-item-update';
    const AGGREGATE_DATA_COMMAND    = 'domain:business:aggregate-data';
    const ARTICLE_UPDATE            = 'domain:article:update';
    const MONGO_AGGREGATE           = 'domain:report-mongo-db:aggregate';
    const MONGO_POPULAR_CATEGORIES  = 'domain:popular-category:update';
    const MONGO_ARCHIVE_RAW         = 'domain:report-mongo-db:archive-raw';
    const MONGO_ARCHIVE_AGGREGATE         = 'domain:report-mongo-db:archive-aggregate';
    const POSTPONE_EXPORT           = 'domain:postpone-export:report';
    const BUSINESS_COUNTER          = 'domain:managed-businesses:counter';
    const POSTPONE_REMOVE           = 'data:postpone:remove';
    const VIDEO_CONVERT             = 'data:video:convert';

    const STATUS_START          = 'START';
    const STATUS_IN_PROGRESS    = 'IN PROGRESS';
    const STATUS_END            = 'DONE';

    /** @var MongoDbManager $mongoDbManager */
    protected $mongoDbManager;

    /**
     * @param MongoDbManager $mongoDbManager
     */
    public function __construct(MongoDbManager $mongoDbManager)
    {
        $this->mongoDbManager = $mongoDbManager;
    }

    /**
     * @param string    $name
     * @param string    $status
     * @param string    $message
     * @param int       $level
     */
    public function addInfo(String $name, String $status, String $message, $level = Logger::INFO)
    {
        $date = $this->mongoDbManager->typeUTCDateTime(new \DateTime());

        $data = [
            self::MONGO_DB_FIELD_DATE_TIME  => $date,
            self::MONGO_DB_FIELD_LEVEL      => $level,
            self::MONGO_DB_FIELD_ACTION     => $name,
            self::MONGO_DB_FIELD_STATUS     => $status,
            self::MONGO_DB_FIELD_MESSAGE    => $message,
            self::MONGO_DB_FIELD_PROCESS_ID => getmypid(),
            self::MONGO_DB_FIELD_CPU_USAGE  => sys_getloadavg()[0],
        ];

        $this->insertCronLog($data);
    }

    /**
     * @param array $data
     */
    protected function insertCronLog($data)
    {
        $this->mongoDbManager->insertOne(
            self::MONGO_DB_COLLECTION_NAME,
            $data
        );
    }
}
