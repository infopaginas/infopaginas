<?php

namespace Domain\SiteBundle\Logger;

use Symfony\Bridge\Monolog\Logger as Monolog;

class CronLogger
{
    private $logger;

    const ELASTIC_SYNC              = 'data:elastic:sync';
    const SUBSC_UPDATE              = 'data:subscription:update';
    const YOUTUBE_UPLOAD            = 'data:youtube-video:upload';
    const WORKING_HOURS_CONVERT     = 'data:working-hours:convert';
    const WORKING_HOURS_UPDATE      = 'data:working-hours:update';
    const DOUBLE_CLICK_SYNC         = 'ipgn:dfp:synch';
    const BUSINESS_UPDATE_STATUS    = 'domain:business:update-status';
    const SUBSCRIPTION_TRACK        = 'ipgn:subscriptions:track';
    const CATALOG_ITEM_UPDATE       = 'domain:business:catalog-item-update';
    const ARTICLE_UPDATE            = 'domain:article:update';
    const MONGO_AGGREGATE           = 'domain:report-mongo-db:aggregate';
    const BUSINESS_COUNTER          = 'domain:managed-businesses:counter';
    const POSTPONE_REMOVE           = 'data:postpone:remove';
    const VIDEO_CONVERT             = 'data:video:convert';

    const STATUS_START = 'START';
    const STATUS_IN_PROGRESS = 'IN PROGRESS';
    const STATUS_END = 'DONE';

    public function __construct(Monolog $channel)
    {
        $this->logger = $channel;
    }

    public function addInfo(String $name, String $status, String $message, $level = 100)
    {
        $this->logger->addRecord($level, ' PHP-' . getmypid() . ' ' . $name . ' ' . $status . ' ' . $message);
    }
}
