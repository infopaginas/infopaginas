<?php

namespace Domain\SiteBundle\Logger;

use Monolog\Logger;
use Oxa\MongoDbBundle\Manager\MongoDbManager;

class CronLogger
{
    public const MONGO_DB_COLLECTION_NAME   = 'cron_log';

    public const MONGO_DB_FIELD_ACTION      = 'action';
    public const MONGO_DB_FIELD_STATUS      = 'status';
    public const MONGO_DB_FIELD_MESSAGE     = 'message';
    public const MONGO_DB_FIELD_LEVEL       = 'level';
    public const MONGO_DB_FIELD_PROCESS_ID  = 'process_id';
    public const MONGO_DB_FIELD_CPU_USAGE   = 'cpu_usage';
    public const MONGO_DB_FIELD_DATE_TIME   = 'datetime';

    public const ELASTIC_SYNC               = 'data:elastic:sync';
    public const YOUTUBE_UPLOAD             = 'data:youtube-video:upload';
    public const WORKING_HOURS_CONVERT      = 'data:working-hours:convert';
    public const WORKING_HOURS_UPDATE       = 'data:working-hours:update';
    public const DOUBLE_CLICK_SYNC          = 'ipgn:dfp:synch';
    public const BUSINESS_UPDATE_STATUS     = 'domain:business:update-status';
    public const SUBSCRIPTION_TRACK         = 'ipgn:subscriptions:track';
    public const CATALOG_ITEM_UPDATE        = 'domain:business:catalog-item-update';
    public const AGGREGATE_DATA_COMMAND     = 'domain:business:aggregate-data';
    public const ARTICLE_UPDATE             = 'domain:article:update';
    public const MONGO_AGGREGATE            = 'domain:report-mongo-db:aggregate';
    public const MONGO_POPULAR_CATEGORIES   = 'domain:popular-category:update';
    public const MONGO_ARCHIVE_RAW          = 'domain:report-mongo-db:archive-raw';
    public const MONGO_ARCHIVE_AGGREGATE    = 'domain:report-mongo-db:archive-aggregate';
    public const POSTPONE_EXPORT            = 'domain:postpone-export:report';
    public const BUSINESS_COUNTER           = 'domain:managed-businesses:counter';
    public const POSTPONE_REMOVE            = 'data:postpone:remove';
    public const VIDEO_CONVERT              = 'data:video:convert';
    public const YOUTUBE_VIDEO_DATA_REFRESH = 'data:youtube-video:refresh';

    public const STATUS_START          = 'START';
    public const STATUS_IN_PROGRESS    = 'IN PROGRESS';
    public const STATUS_END            = 'DONE';

    // todo: replace
    public const MESSAGE_START = 'execute:start';
    public const MESSAGE_STOP  = 'execute:stop';

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
